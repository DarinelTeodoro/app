<?php
header('Content-Type: application/json');
include('../../model/db.php');

$order = $_GET['order'] ?? null;

if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Falta el parámetro order']);
    exit;
}

try {
    $data_offer = data_offers($order);

    echo json_encode([
        'success' => true,
        'data' => $data_offer ? array_map(fn($o) => [
            'id' => $o['id'],
            'type' => $o['type'],
            'value' => $o['value'],
            'value_calculado' => $o['value_calculado'],
            'date' => $o['date'],
        ], $data_offer) : []
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al consultar descuentos']);
}