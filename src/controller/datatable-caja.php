<?php
include('../model/db.php');
header('Content-Type: application/json');

try {
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM adjustment_checkout
            ORDER BY fecha DESC");
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

    $data = [];

    if ($rows) {
        foreach ($rows as $row) {
            $ln['cantidad'] = $row['tipo'] == 'resta' ? '<b class="text-danger">-</b> $' . number_format(($row['cantidad']), 2) : '<b class="text-success">+</b> $' . number_format($row['cantidad'], 2);
            $ln['fecha'] = date('d-M-Y', strtotime($row['fecha']));
            $ln['hora'] = date('H:i:s', strtotime($row['fecha']));
            $ln['motivo'] = $row['motivo'];
            $data[] = $ln;
        }
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>