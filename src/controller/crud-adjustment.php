<?php
include('../model/db.php');
date_default_timezone_set('America/Mexico_City');

function insert_movement($conexion, $id, $e, $t, $c) {
    $info_materia = materia($e);
    $units = (float) $c * (float) $info_materia['content_presentation'];
    $amount = (float) $units * (float) $info_materia['content_unit'];

    if ($t == 'plus') {
        $total = (float) $amount + (float) $info_materia['total'];
    } else if ($t == 'less') {
        $total = (float) $info_materia['total'] - (float) $amount;
    }

    $insert = $conexion->prepare('INSERT INTO materia_purchases(adjustment, materia, qty, units, amount) VALUES (:adjustment, :materia, :qty, :units, :amount)');
    $insert->bindParam(':adjustment', $id);
    $insert->bindParam(':materia', $e);
    $insert->bindParam(':qty', $c);
    $insert->bindParam(':units', $units);
    $insert->bindParam(':amount', $amount);

    $update = $conexion->prepare('UPDATE materia SET total = :amount WHERE id = :id');
    $update->bindParam(':amount', $total);
    $update->bindParam(':id', $e);

    $insert->execute();
    $update->execute();
}

if ($_POST['request'] == 'create') {

    $type    = $_POST['type-adjustment'] ?? '';
    $materias = $_POST['materias'] ?? [];

    if (!isset($_POST['materias'])) {
        $response['status']  = 400;
        $response['title']   = 'Alerta';
        $response['message'] = 'Agregue al menos un ingrediente o materia prima';
        echo json_encode($response);
        exit;
    }

    $conexion = new Conexion();

    try {
        $conexion->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $insert = $conexion->prepare('INSERT INTO materia_adjustment(date, adjustment) VALUES (:date, :adjustment)');
        $insert->bindParam(':date', $date);
        $insert->bindParam(':adjustment', $type);
        $insert->execute();
        $adjustment_id = $conexion->lastInsertId();

        foreach ($materias as $m) {
            $materia = (int)   $m['id'];
            $cantidad = (float) $m['amount'];

            insert_movement($conexion, $adjustment_id, $materia, $type, $cantidad);
        }

        $conexion->commit();

        $response['status']  = 201;
        $response['title']   = 'Movimiento Añadido';
        $response['message'] = 'El inventario se ha actualizado';

    } catch (Exception $e) {
        $conexion->rollBack();

        $response['status']  = 500;
        $response['title']   = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>