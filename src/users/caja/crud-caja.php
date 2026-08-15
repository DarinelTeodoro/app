<?php
session_start();
include('../../model/db.php');
date_default_timezone_set('America/Mexico_City');


if ($_POST['request'] == 'update-amountInit') {

    $cantidad = $_POST['amount-init'] ?? 0;

    if ($cantidad < 0) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'Ingrese una cantidad valida';
        echo json_encode($response);
        exit;
    }

    $conexion = new Conexion();

    try {
        $conexion->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $update = $conexion->prepare('UPDATE init_daily SET inicial = :inicial');
        # $update->bindParam(':dt', $date);
        $update->bindParam(':inicial', $cantidad);
        $update->execute();

        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Monto Actualizado';
        $response['message'] = 'El monto inicial ha sido actualizado';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'update-adjustmentBox') {

    $tipo = $_POST['type-adjustment'];
    $cantidad = $_POST['amount-adjustment'] ?? 0;
    $user = $_SESSION['data-useractive'];
    $motivo = $_POST['motivo-adjustment'];
    $fecha = date('Y-m-d H:i:s');

    if ($cantidad <= 0) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'Ingrese una cantidad valida';
        echo json_encode($response);
        exit;
    }

    if (empty($motivo)) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'Justifique el motivo del ajuste';
        echo json_encode($response);
        exit;
    }

    $conexion = new Conexion();

    try {
        $conexion->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $insert = $conexion->prepare('INSERT INTO adjustment_checkout(tipo, cantidad, user, motivo, fecha) VALUES (:tipo, :cantidad, :user, :motivo, :fecha)');
        $insert->bindParam(':tipo', $tipo);
        $insert->bindParam(':cantidad', $cantidad);
        $insert->bindParam(':user', $user);
        $insert->bindParam(':motivo', $motivo);
        $insert->bindParam(':fecha', $fecha);
        $insert->execute();

        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Ajuste Agregado';
        $response['message'] = 'Se agrego un nuevo ajuste al calculo de caja';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'update-cutBox') {
    $creador = get_init();

    $user = $_SESSION['data-useractive'];
    $fecha = date('Y-m-d H:i:s');
    $total_ingresado = intval($_POST['mil']) + intval($_POST['quinientos']) + intval($_POST['doscientos']) + intval($_POST['cien']) + intval($_POST['cincuenta']) + intval($_POST['veinte']) + floatval($_POST['monedas']);

    $data_init = get_init();
    $idinit = $data_init['id'];

    $conexion = new Conexion();

    $sql = $conexion->prepare("SELECT SUM(received_one) AS cuenta, SUM(sobrante) AS cambio FROM transactions WHERE method = 'efectivo' AND date >= :di AND date < :df");
    $sql->bindParam(':di', $data_init['dt']);
    $sql->bindParam(':df', $fecha);
    $sql->execute();
    $tve = $sql->fetch(PDO::FETCH_ASSOC);

    $sql_mix = $conexion->prepare("SELECT SUM(received_one) AS cuenta, SUM(tip_one) AS propina FROM transactions WHERE method = 'mixto' AND date >= :di AND date < :df");
    $sql_mix->bindParam(':di', $data_init['dt']);
    $sql_mix->bindParam(':df', $fecha);
    $sql_mix->execute();
    $tvm = $sql_mix->fetch(PDO::FETCH_ASSOC);

    $sql_resta = $conexion->prepare("SELECT SUM(cantidad) AS resta FROM adjustment_checkout WHERE tipo = 'resta' AND fecha >= :di AND fecha < :df AND used = 0");
    $sql_resta->bindParam(':di', $data_init['dt']);
    $sql_resta->bindParam(':df', $fecha);
    $sql_resta->execute();
    $resta = $sql_resta->fetch(PDO::FETCH_ASSOC);

    $sql_suma = $conexion->prepare("SELECT SUM(cantidad) AS suma FROM adjustment_checkout WHERE tipo = 'suma' AND fecha >= :di AND fecha < :df AND used = 0");
    $sql_suma->bindParam(':di', $data_init['dt']);
    $sql_suma->bindParam(':df', $fecha);
    $sql_suma->execute();
    $suma = $sql_suma->fetch(PDO::FETCH_ASSOC);

    $total_ventas = ((float) $tve['cuenta'] - (float) $tve['cambio']) + ((float) $tvm['cuenta'] + (float) $tvm['propina']);
    $total_ajustes = (float) $suma['suma'] - (float) $resta['resta'];
    $total_real = (float) $data_init['inicial'] + (float) $total_ventas + (float) $total_ajustes;

    if ($total_ingresado == $total_real) {
        $resultado = 'exacto';
        $diferencia = 0;

        $mensaje = 'El conteo de la caja coincide correctamente';
    } else if ($total_ingresado > $total_real) {
        $resultado = 'sobro';
        $diferencia = (float) $total_ingresado - (float) $total_real;

        $mensaje = 'Hay $'.number_format($diferencia).' de mas en la caja';
    }else if ($total_ingresado < $total_real) {
        $resultado = 'falto';
        $diferencia = (float) $total_real - (float) $total_ingresado;

        $mensaje = 'Hubo un faltante de $'.number_format($diferencia).' en la caja';
    }

    try {
        $conexion->beginTransaction();

        $insert = $conexion->prepare('INSERT INTO cut_checkout(user, fecha, cantidad_ingresado, cantidad_real, resultado, diferencia, mil, quinientos, doscientos, cien, cincuenta, veinte, monedas) VALUES (:user, :fecha, :cantidad_ingresado, :cantidad_real, :resultado, :diferencia, :mil, :quinientos, :doscientos, :cien, :cincuenta, :veinte, :monedas)');
        $insert->bindParam(':user', $user);
        $insert->bindParam(':fecha', $fecha);
        $insert->bindParam(':cantidad_ingresado', $total_ingresado);
        $insert->bindParam(':cantidad_real', $total_real);
        $insert->bindParam(':resultado', $resultado);
        $insert->bindParam(':diferencia', $diferencia);
        $insert->bindParam(':mil', $_POST['mil']);
        $insert->bindParam(':quinientos', $_POST['quinientos']);
        $insert->bindParam(':doscientos', $_POST['doscientos']);
        $insert->bindParam(':cien', $_POST['cien']);
        $insert->bindParam(':cincuenta', $_POST['cincuenta']);
        $insert->bindParam(':veinte', $_POST['veinte']);
        $insert->bindParam(':monedas', $_POST['monedas']);
        $insert->execute();

        $update = $conexion->prepare("UPDATE init_daily SET used = 1 WHERE id = :id");
        $update->bindParam(':id', $idinit);
        $update->execute();

        $update_adjustment = $conexion->prepare("UPDATE adjustment_checkout SET used = 1 WHERE used = 0 AND fecha >= :di AND fecha < :df");
        $update_adjustment->bindParam(':di', $data_init['dt']);
        $update_adjustment->bindParam(':df', $fecha);
        $update_adjustment->execute();

        $insert_init = $conexion->prepare("INSERT INTO init_daily(dt, inicial) VALUES (:dt, 0)");
        $insert_init->bindParam(':dt', $fecha);
        $insert_init->execute();

        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Corte Procesado';
        $response['message'] = $mensaje;

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>