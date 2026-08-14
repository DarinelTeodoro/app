<?php
session_start();
include('../../model/db.php');
date_default_timezone_set('America/Mexico_City');


if ($_POST['request'] == 'update-amountInit') {

    $cantidad    = $_POST['amount-init'] ?? 0;

    if ($cantidad < 0) {
        $response['status']  = 400;
        $response['title']   = 'Alerta';
        $response['message'] = 'Ingrese una cantidad valida';
        echo json_encode($response);
        exit;
    }

    $conexion = new Conexion();

    try {
        $conexion->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $update = $conexion->prepare('UPDATE init_daily SET dt = :dt, inicial = :inicial');
        $update->bindParam(':dt', $date);
        $update->bindParam(':inicial', $cantidad);
        $update->execute();

        $conexion->commit();

        $response['status']  = 201;
        $response['title']   = 'Monto Actualizado';
        $response['message'] = 'El monto inicial ha sido actualizado';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status']  = 500;
        $response['title']   = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'update-adjustmentBox') {

    $tipo = $_POST['type-adjustment'];
    $cantidad    = $_POST['amount-adjustment'] ?? 0;
    $user = $_SESSION['data-useractive'];
    $motivo = $_POST['motivo-adjustment'];
    $fecha = date('Y-m-d H:i:s');

    if ($cantidad <= 0) {
        $response['status']  = 400;
        $response['title']   = 'Alerta';
        $response['message'] = 'Ingrese una cantidad valida';
        echo json_encode($response);
        exit;
    }

    if (empty($motivo)) {
        $response['status']  = 400;
        $response['title']   = 'Alerta';
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

        $response['status']  = 201;
        $response['title']   = 'Ajuste Agregado';
        $response['message'] = 'Se agrego un nuevo ajuste al calculo de caja';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status']  = 500;
        $response['title']   = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>