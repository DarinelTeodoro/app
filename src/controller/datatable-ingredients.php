<?php
include('../model/db.php');
header('Content-Type: application/json');

try {
    $rows = materias('ingredient');
    $data = [];

    if ($rows) {
        foreach ($rows as $row) {
            $total = $row['total'];
            $metric = $row['metric'];

            if ($metric == 'gramos') {
                $value = $total / 1000;
                $unit = 'kg';
            } elseif ($metric == 'mililitros') {
                $value = $total / 1000;
                $unit = 'lt';
            } else {
                $value = (int) $total;
                $unit = 'pz';
            }

            $ln['ingredient'] = $row['materia'];
            $ln['reserva'] = $value . ' ' . $unit;
            $ln['actions'] = '
            <button class="btn-edit" onclick="edit_ingredient(' . $row['id'] . ')">
                <i class="fi fi-br-pencil"></i>
            </button>
            <button class="btn-delete" onclick="delete_ingredient(' . $row['id'] . ')">
                <i class="fi fi-br-trash"></i>
            </button>
            ';
            $data[] = $ln;
        }
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>