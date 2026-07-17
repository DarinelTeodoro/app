<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-primary'];
        $presentation = $_POST['mode-primary'];
        $content = $_POST['content-primary'];
        $piece = $_POST['piece-primary'];

        // Validar datos obligatorios
        if (empty($name) || empty($content) || empty($piece)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios y verifique que sean validos';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO materia(materia, type, presentation, content_presentation, metric, content_unit) VALUES (:materia, "primary", :presentation, :content, "unidades", :piece)');
        $insert->bindParam(':materia', $name);
        $insert->bindParam(':presentation', $presentation);
        $insert->bindParam(':content', $content);
        $insert->bindParam(':piece', $piece);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Extra';
        $response['message'] = 'Se agrego "' . $name . '" a la lista de materias primas';
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
        $id_primary = $_POST['primary'];

        $delete = $conexion->prepare('DELETE FROM materia WHERE id = :id');
        $delete->bindParam(':id', $id_primary);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Materia Eliminada';
        $response['message'] = 'La materia ha sido eliminada de la lista';
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
        $primaryid = $_POST['id-primary-edit'];
        $name = $_POST['name-primary-edit'];
        $presentation = $_POST['mode-primary-edit'];
        $content = $_POST['content-primary-edit'];
        $piece = $_POST['piece-primary-edit'];

        // Validar datos obligatorios
        if (empty($name) || empty($content) || empty($piece)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios y verifique que sean validos';
            echo json_encode($response);
            exit;
        }

        $update = $conexion->prepare('UPDATE materia SET materia = :materia, presentation = :presentation, content_presentation = :content, content_unit = :piece WHERE id = :id');
        $update->bindParam(':materia', $name);
        $update->bindParam(':presentation', $presentation);
        $update->bindParam(':content', $content);
        $update->bindParam(':piece', $piece);
        $update->bindParam(':id', $primaryid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'La materia ha sido actualizado';
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