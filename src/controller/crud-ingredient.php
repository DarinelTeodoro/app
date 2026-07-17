<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-ingredient'];
        $presentation = $_POST['mode-ingredient'];
        $content_presentation = $_POST['contpre-ingredient'];
        $metric = $_POST['metric-ingredient'];
        $content_unit = $_POST['contunit-ingredient'];

        // Validar datos obligatorios
        if (empty($name) || empty($content_presentation) || empty($content_unit)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios y verifique que sean validos';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO materia(materia, type, presentation, content_presentation, metric, content_unit) VALUES (:materia, "ingredient", :presentation, :content_presentation, :metric, :content_unit)');
        $insert->bindParam(':materia', $name);
        $insert->bindParam(':presentation', $presentation);
        $insert->bindParam(':content_presentation', $content_presentation);
        $insert->bindParam(':metric', $metric);
        $insert->bindParam(':content_unit', $content_unit);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Extra';
        $response['message'] = 'Se agrego "' . $name . '" a la lista de ingredientes';
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
        $id_ingredient = $_POST['ingredient'];

        $delete = $conexion->prepare('DELETE FROM materia WHERE id = :id');
        $delete->bindParam(':id', $id_ingredient);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Ingrediente Eliminado';
        $response['message'] = 'El ingrediente ha sido eliminada de la lista';
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
        $ingredientid = $_POST['id-ingredient-edit'];
        $name = $_POST['name-ingredient-edit'];
        $presentation = $_POST['mode-ingredient-edit'];
        $content_presentation = $_POST['contpre-ingredient-edit'];
        $metric = $_POST['metric-ingredient-edit'];
        $content_unit = $_POST['contunit-ingredient-edit'];

        // Validar datos obligatorios
        if (empty($name) || empty($content_presentation) || empty($content_unit)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios y verifique que sean validos';
            echo json_encode($response);
            exit;
        }

        $update = $conexion->prepare('UPDATE materia SET materia = :materia, presentation = :presentation, content_presentation = :cp, metric = :metric, content_unit = :cu WHERE id = :id');
        $update->bindParam(':materia', $name);
        $update->bindParam(':presentation', $presentation);
        $update->bindParam(':cp', $content_presentation);
        $update->bindParam(':metric', $metric);
        $update->bindParam(':cu', $content_unit);
        $update->bindParam(':id', $ingredientid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'El ingrediente ha sido actualizado';
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