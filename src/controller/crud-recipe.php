<?php
include('../model/db.php');
date_default_timezone_set('America/Mexico_City');

function insert_recipe($conexion, $product, $type_product, $materia, $value) {
    $table_materia = materia($materia);
    $type_materia = $table_materia['type'];

    $insert = $conexion->prepare('INSERT INTO recipe(product, type_product, materia, type_materia, value) VALUES (:product, :tp, :materia, :tm, :val)');
    $insert->bindParam(':product', $product);
    $insert->bindParam(':tp', $type_product);
    $insert->bindParam(':materia', $materia);
    $insert->bindParam(':tm', $type_materia);
    $insert->bindParam(':val', $value);
    $insert->execute();
}

if ($_POST['request'] == 'create') {

    $product    = $_POST['id-item'] ?? '';
    $type_product    = $_POST['type-item'] ?? '';
    $items = $_POST['items'] ?? [];

    if (!isset($_POST['items'])) {
        $response['status']  = 400;
        $response['title']   = 'Alerta';
        $response['message'] = 'Agregue al menos un ingrediente o materia prima';
        echo json_encode($response);
        exit;
    }

    $conexion = new Conexion();

    try {
        $conexion->beginTransaction();

        foreach ($items as $i) {
            $materia = $i['materia'];
            $value = $i['value'];
            insert_recipe($conexion, $product, $type_product, $materia, $value);
        }

        $conexion->commit();

        $response['status']  = 201;
        $response['title']   = 'Receta Añadida';
        $response['message'] = 'Las recetas se han actualizado';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status']  = 500;
        $response['title']   = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'delete') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $id_materia = $_POST['materia'];

        $delete = $conexion->prepare('DELETE FROM recipe WHERE materia = :id');
        $delete->bindParam(':id', $id_materia);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Ingrediente / Materia Eliminado';
        $response['message'] = 'El ingrediente / materia prima fue removida de la receta';
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