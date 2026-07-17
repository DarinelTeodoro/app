<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-category'];
        $description = $_POST['description-category'];
        $destination = $_POST['destination-category'];

        // Validar datos obligatorios
        if (empty($name)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO category(category, description, destination) VALUES (:category, :description, :destination)');
        $insert->bindParam(':category', $name);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':destination', $destination);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nueva Categoria';
        $response['message'] = 'Se agrego la categoria "' . $name . '"';
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
        $id_category = $_POST['category'];

        $delete = $conexion->prepare('DELETE FROM category WHERE id = :id');
        $delete->bindParam(':id', $id_category);
        $delete->execute();

        $update = $conexion->prepare('UPDATE product SET category = 1 WHERE category = :category');
        $update->bindParam(':category', $id_category);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Categoria Eliminada';
        $response['message'] = 'La categoria ha sido eliminada de la lista';
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
        $categoryid = $_POST['id-category-edit'];
        $category = $_POST['name-category-edit'];
        $description = $_POST['description-category-edit'];
        $destination = $_POST['destination-category-edit'];

        // Validar datos obligatorios
        if (empty($categoryid) || empty($category)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }


        $update = $conexion->prepare('UPDATE category SET category = :category, description = :description, destination = :destination WHERE id = :id');
        $update->bindParam(':category', $category);
        $update->bindParam(':description', $description);
        $update->bindParam(':destination', $destination);
        $update->bindParam(':id', $categoryid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'La categoria ha sido actualizada';
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