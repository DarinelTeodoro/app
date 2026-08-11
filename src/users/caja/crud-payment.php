<?php
session_start();
include('../../model/db.php');
date_default_timezone_set('America/Mexico_City');
$user_loged = $_SESSION['data-useractive'];
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    throw new Exception('JSON inválido.');
}

$conexion = new Conexion();
$conexion->beginTransaction();

function insert_payment($conexion, $orden, $method, $base, $tip_one, $received_one, $tip_two, $received_two, $total, $sobrante, $debt, $separated)
{
    $fecha = date('Y-m-d H:i:s');
    if ($method == 'mixto') {
        $neto_cuenta = $base;
    } else {
        $neto_cuenta = (float) $received_one + (float) $received_two - (float) $tip_one - (float) $tip_two - (float) $sobrante;
    }

    $insert = $conexion->prepare("INSERT INTO transactions(orden, method, base, tip_one, received_one, tip_two, received_two, total, sobrante, neto_cuenta, debt, date) VALUES (:orden, :method, :base, :tip_one, :received_one, :tip_two, :received_two, :total, :sobrante, :neto_cuenta, :debt, :date)");
    $insert->bindParam(':orden', $orden);
    $insert->bindParam(':method', $method);
    $insert->bindParam(':base', $base);
    $insert->bindParam(':tip_one', $tip_one);
    $insert->bindParam(':received_one', $received_one);
    $insert->bindParam(':tip_two', $tip_two);
    $insert->bindParam(':received_two', $received_two);
    $insert->bindParam(':total', $total);
    $insert->bindParam(':sobrante', $sobrante);
    $insert->bindParam(':neto_cuenta', $neto_cuenta);
    $insert->bindParam(':debt', $debt);
    $insert->bindParam(':date', $fecha);

    if (!$insert->execute()) {
        throw new Exception('No se pudo registrar el pago.');
    }

    $query = $conexion->prepare("SELECT * FROM view_order WHERE id = :orden");
    $query->bindParam(':orden', $orden);
    $query->execute();
    $data_pays = $query->fetch(PDO::FETCH_ASSOC);

    if ($data_pays['paid'] >= $data_pays['total']) {
        $update = $conexion->prepare("UPDATE sale_order SET status = 'finalizado', finished_at = :finished WHERE id = :orden");
        $update->bindParam(':orden', $orden);
        $update->bindParam(':finished', $fecha);

        if (!$update->execute()) {
            throw new Exception('No se pudo finalizar la comanda.');
        }
    } else {
        if ($separated == false) {
            $update_deposit = $conexion->prepare("UPDATE sale_order SET deposit = 1 WHERE id = :orden");
            $update_deposit->bindParam(':orden', $orden);

            if (!$update_deposit->execute()) {
                throw new Exception('No se pudo actualizar la comanda.');
            }
        }
    }

    // Devolvemos el estado ya actualizado de la orden (paid/debt reales, calculados por la vista)
    return $data_pays;
}

function update_items_payed($conexion, $item, $qty)
{
    $update = $conexion->prepare("UPDATE items SET payed = payed + :qty WHERE id = :item");
    $update->bindParam(':item', $item);
    $update->bindParam(':qty', $qty);

    if (!$update->execute()) {
        throw new Exception('No se pudo actualizar el pago del item.');
    }
}

try {
    $orden = $data['order_id'] ?? null;
    $metodo = $data['method'] ?? null;

    if (!$orden || !$metodo) {
        throw new Exception('Datos incompletos.');
    }

    $separado = !empty($data['item_ids']);
    $orden_actualizada = null;
    $monto_cobrado_ahora = 0;

    if ($metodo == 'efectivo') {
        $cuenta_base = $data['base'];
        $total = $data['total'];
        $propina = $data['tip'];
        $recibido = $data['received'];   // ya incluye propina
        $cambio = $data['change'] ?? 0;
        $deuda = $data['debt'] ?? 0;

        $orden_actualizada = insert_payment($conexion, $orden, $metodo, $cuenta_base, $propina, $recibido, 0, 0, $total, $cambio, $deuda, $separado);
        $monto_cobrado_ahora = $total;

        if (!empty($data['item_ids'])) {
            foreach ($data['item_ids'] as $item) {
                update_items_payed($conexion, $item['id'], $item['qty']);
            }
        }

    } else if ($metodo == 'tarjeta' || $metodo == 'transferencia') {
        $cuenta_base = $data['base'];
        $total = $data['total'];
        $propina = (float) $data['tip'];
        $abono = (float) ($data['partial'] ?? $data['base']); // solo base, SIN propina
        $deuda = $data['debt'] ?? 0;

        // insert_payment espera "recibido" incluyendo propina, igual que en efectivo
        $recibido_bruto = $abono + $propina;

        $orden_actualizada = insert_payment($conexion, $orden, $metodo, $cuenta_base, $propina, $recibido_bruto, 0, 0, $total, 0, $deuda, $separado);
        $monto_cobrado_ahora = $total;

        if (!empty($data['item_ids'])) {
            foreach ($data['item_ids'] as $item) {
                update_items_payed($conexion, $item['id'], $item['qty']);
            }
        }

    } else if ($metodo == 'mixto') {
        $cuenta_base = $data['base'];
        $total = $data['total'];
        $deuda = $data['debt'] ?? 0;

        // details trae: cash, cashTip, card, cardTip (cash/card SIN propina)
        $details = $data['details'];
        $efectivo = (float) $details['cash'];
        $prop_ef = (float) $details['cashTip'];
        $tarjeta = (float) $details['card'];
        $prop_tar = (float) $details['cardTip'];

        // igual aquí: recibido debe incluir la propina de cada parte
        # $recibido_efectivo = $efectivo + $prop_ef;
        # $recibido_tarjeta = $tarjeta + $prop_tar;

        $orden_actualizada = insert_payment($conexion, $orden, $metodo, $cuenta_base, $prop_ef, $efectivo, $prop_tar, $tarjeta, $total, 0, $deuda, $separado);
        $monto_cobrado_ahora = $total;

        if (!empty($data['item_ids'])) {
            foreach ($data['item_ids'] as $item) {
                update_items_payed($conexion, $item['id'], $item['qty']);
            }
        }
    } else {
        throw new Exception('Método de pago no reconocido.');
    }

    $conexion->commit();

    $response['status'] = 201;
    $response['title'] = 'Pago recibido';
    $response['message'] = 'Hemos recibido el pago exitosamente';
    $response['order'] = $orden;
    $response['received'] = $monto_cobrado_ahora;                 // lo cobrado en ESTA transacción
    $response['debt'] = (float) ($orden_actualizada['debt'] ?? 0); // lo que falta, ya recalculado por la vista
    $response['paid_total'] = (float) ($orden_actualizada['paid'] ?? 0); // total abonado acumulado
} catch (Exception $e) {
    $conexion->rollBack();

    $response['status'] = 500;
    $response['title'] = 'Error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>