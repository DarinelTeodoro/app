<?php
include('../model/db.php');
header('Content-Type: application/json');

try {
    $rows = adjustments();
    $data = [];

    if ($rows) {
        foreach ($rows as $row) {
            $fecha = $row['date'];
            $ln['group'] =  date('Y-m-d', strtotime($fecha));
            $ln['date'] = $row['date'];
            $ln['adjustment'] = $row['adjustment'] == 'plus' ? 'Ingredientes / Materias <span class="text-success">agregadas al inventario</span>' : 'Ingredientes / Materias <span class="text-danger">descontadas del inventario</div>';
            $ln['actions'] = '<button class="btn-edit" onclick="details_movement(' . $row['id'] . ')"><i class="fi fi-br-attention-detail"></i></button>';
            $data[] = $ln;
        }
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>