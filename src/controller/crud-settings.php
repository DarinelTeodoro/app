<?php
include('../model/db.php');

if ($_POST['request'] == 'edit-tables') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $n_mesas = $_POST['n-tables-edit'];
        // Validar datos obligatorios
        if (empty($n_mesas)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }


        $update = $conexion->prepare('UPDATE settings SET value = :val WHERE keyword = "tables"');
        $update->bindParam(':val', $n_mesas);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'Se cambio el numero de mesas que hay en el local';
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