<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-extra'];
        $destination = $_POST['destination-extra'];
        $price = $_POST['price-extra'];

        // Validar datos obligatorios
        if (empty($name) || $price === null) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO extra(extra, destination, price) VALUES (:extra, :destination, :price)');
        $insert->bindParam(':extra', $name);
        $insert->bindParam(':destination', $destination);
        $insert->bindParam(':price', $price);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Extra';
        $response['message'] = 'Se agrego el extra "'.$name.'"';
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'delete') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $id_extra = $_POST['extra'];

        $delete = $conexion->prepare('DELETE FROM extra WHERE id = :id');
        $delete->bindParam(':id', $id_extra);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Extra Eliminado';
        $response['message'] = 'El extra ha sido eliminada de la lista';
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'edit') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $idextra = $_POST['id-extra-edit'];
        $name = $_POST['name-extra-edit'];
        $destination = $_POST['destination-extra-edit'];
        $price = $_POST['price-extra-edit'];

        // Validar datos obligatorios
        if (empty($idextra) || empty($name) || $price === null) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }


        $update = $conexion->prepare('UPDATE extra SET extra = :extra, destination = :destination, price = :price WHERE id = :id');
        $update->bindParam(':extra', $name);
        $update->bindParam(':destination', $destination);
        $update->bindParam(':price', $price);
        $update->bindParam(':id', $idextra);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'El extra ha sido actualizado';
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