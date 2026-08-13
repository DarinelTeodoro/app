<?php
include('../../model/db.php');
date_default_timezone_set('America/Mexico_City');

if ($_POST['request'] == 'order-cancel') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $order = $_POST['order-to-cancel'];
        $motivo = $_POST['motivo-cancel'];

        if (empty($motivo)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Describa el motivo de la cancelación';
            echo json_encode($response);
            exit;
        }

        $update = $conexion->prepare('UPDATE sale_order SET status = "cancelado", note = :motivo WHERE id = :order');
        $update->bindParam(':motivo', $motivo);
        $update->bindParam(':order', $order);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Orden Cancelada';
        $response['message'] = 'El estatus de la orden fue actualizada';
        $response['order'] = $order;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}

if ($_POST['request'] == 'delete-item') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $order = $_POST['orden'];
        $item = $_POST['item'];

        $main_delete = $conexion->prepare('DELETE FROM items WHERE id = :id');
        $main_delete->bindParam(':id', $item);
        $main_delete->execute();

        $extras_delete = $conexion->prepare('DELETE FROM items_extras WHERE id_item = :item');
        $extras_delete->bindParam(':item', $item);
        $extras_delete->execute();

        $combo_delete = $conexion->prepare('DELETE FROM combo_item_selected WHERE item = :item');
        $combo_delete->bindParam(':item', $item);
        $combo_delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Producto Eliminado';
        $response['message'] = 'El producto se quito de la orden';
        $response['order'] = $order;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}

if ($_POST['request'] == 'order-offer') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $order = $_POST['order-to-offer'];
        $type = $_POST['type-offer'];
        $value = $_POST['value-offer'];
        $motivo = $_POST['motivo-offer'];
        $fecha = date('Y-m-d H:i:s');

        if (empty($motivo) || empty($value) || $value <= 0) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje campos vacios / No introducza datos invalidos';
            echo json_encode($response);
            exit;
        }

        $data_order = data_order($order);

        if ($type == 'porcentaje') {
            $calculado = ((float) $data_order['total'] / 100) * $value;
            $message = 'Se aplico un descuento del '. $value . '%';
        } else if ($type == 'fijo') {
            $calculado = $value;
            $message = 'Se aplico un descuento de $'. number_format($value,2) . ' pesos';
        }

        if ($data_order['debt'] < $calculado) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'El descuento no puede ser mayor al total de la comanda o al faltante por pagar';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO offer(date, orden, type, value, value_calculado) VALUES (:date, :orden, :type, :value, :value_calculado)');
        $insert->bindParam(':date', $fecha);
        $insert->bindParam(':orden', $order);
        $insert->bindParam(':type', $type);
        $insert->bindParam(':value', $value);
        $insert->bindParam(':value_calculado', $calculado);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Descuento Aplicado';
        $response['message'] = $message;
        $response['order'] = $order;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'delete-offer') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $order = $_POST['orden'];
        $offer = $_POST['offer'];

        $main_delete = $conexion->prepare('DELETE FROM offer WHERE id = :id');
        $main_delete->bindParam(':id', $offer);
        $main_delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Descuento Eliminado';
        $response['message'] = 'El descuento ha sido removido de la orden';
        $response['order'] = $order;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>