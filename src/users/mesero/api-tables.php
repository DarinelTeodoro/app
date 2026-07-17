<?php
header('Content-Type: application/json; charset=utf-8');
include('../../model/db.php');

try {
    // Obtener número total de mesas
    $setting = settings('tables');
    $total_tables = $setting ? (int) $setting['value'] : 0;

    // Obtener mesas ocupadas (órdenes activas, ni finalizadas ni canceladas)
    $tables_disabled = tables_disabled();
    $ocupadas = array_map('intval', $tables_disabled);

    // Construir lista de mesas con su estado
    $mesas = [];
    for ($i = 1; $i <= $total_tables; $i++) {
        $mesas[] = [
            'n_table' => $i,
            'ocupada' => in_array($i, $ocupadas)
        ];
    }

    echo json_encode(['success' => true, 'tables' => $mesas]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al consultar mesas']);
}
?>