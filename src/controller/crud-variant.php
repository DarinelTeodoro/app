<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-variant'];
        $product = $_POST['product-variant'];
        $plus = $_POST['plus-variant'];

        // Validar datos obligatorios
        if (empty($name) || $plus === null) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        $data = product($product);

        $insert = $conexion->prepare('INSERT INTO variant(product, variant, increase) VALUES (:product, :variant, :plus)');
        $insert->bindParam(':product', $product);
        $insert->bindParam(':variant', $name);
        $insert->bindParam(':plus', $plus);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nueva Variante';
        $response['message'] = 'Se agrego una variante del producto "'.$data['product'].'"';
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
        $id_variant = $_POST['variant'];

        $delete = $conexion->prepare('DELETE FROM variant WHERE id = :id');
        $delete->bindParam(':id', $id_variant);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Variante Eliminada';
        $response['message'] = 'La variante ha sido eliminada de la lista';
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
        $variantid = $_POST['id-variant-edit'];
        $productid = $_POST['product-variant-edit'];
        $variant = $_POST['name-variant-edit'];
        $plus = $_POST['plus-variant-edit'];

        // Validar datos obligatorios
        if (empty($variantid) || empty($variant) || $plus === null) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }


        $update = $conexion->prepare('UPDATE variant SET product = :product, variant = :variant, increase = :plus WHERE id = :id');
        $update->bindParam(':product', $productid);
        $update->bindParam(':variant', $variant);
        $update->bindParam(':plus', $plus);
        $update->bindParam(':id', $variantid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'La variante ha sido actualizada';
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