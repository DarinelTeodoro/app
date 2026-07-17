<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');
include('../../model/db.php');

function build_orders($orders)
{
    if (!$orders)
        return [];

    $resultado = [];
    foreach ($orders as $order) {

        $resultado[] = [
            'id' => (int) $order['id'],
            'delivery' => $order['delivery'],
            'mesa' => (int) $order['n_table'],
            'client' => $order['client'],
            'estado' => $order['status'],
            'creada_en' => strtotime($order['modified_at']) * 1000,
            // finished_at puede venir NULL si la orden todavía no se finalizó/canceló
            'finalizada_en' => $order['finished_at'] ? strtotime($order['finished_at']) * 1000 : null,
            'total' => $order['total'],
            'notes' => $order['note'],
            'cocina' => $order['cocina'],
            'barra' => $order['barra']
        ];
    }
    return $resultado;
}

try {
    $pending = pending_orders();
    $data = build_orders($pending);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'hora' => date('H:i:s')
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al consultar órdenes pendientes']);
}