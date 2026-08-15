<?php
header('Content-Type: application/json; charset=utf-8');
include('../model/db.php');

function group_variants($rows)
{
    if (!$rows)
        return [];

    $agrupado = [];
    foreach ($rows as $row) {
        $agrupado[$row['product']][] = [
            'id' => $row['id'],
            'variant' => $row['variant'],
            'price' => $row['increase'],
            'available' => $row['available'],
        ];
    }
    return $agrupado;
}

try {
    $data = view_menu();
    $extras = extras();
    $variants = group_variants(variants());

    // Armar combos agrupados por grupo
    $combos = [];

    foreach ($data as $producto) {
        if ($producto['type'] !== 'combo')
            continue;

        $rows = combo_items($producto['id']);
        if (!$rows)
            continue;

        $grupos = [];
        foreach ($rows as $row) {
            $gid = $row['combo_group'];
            if (!isset($grupos[$gid])) {
                $grupos[$gid] = [
                    'group_id' => $gid,
                    'group_name' => $row['group_name'],
                    'selection_type' => $row['type_group'],
                    'instruction' => $row['instruction'],
                    'products' => []
                ];
            }

            $itemId = match ($row['type']) {
                'variante' => $row['variant'],
                'extra' => $row['extra'],
                'producto' => $row['product'],
            };
 
            $destinationId = match ($row['type']) {
                'variante' => $row['product'],
                'extra' => $row['extra'],
                'producto' => $row['product'],
            };

            $grupos[$gid]['products'][] = [
                'id' => $itemId,
                'destination' => get_destination($row['type'], $destinationId),
                'name' => $row['name'],
                'type' => $row['type'],
                'qty' => (int) $row['qty']
            ];
        }

        $combos[$producto['id']] = array_values($grupos);
    }

    $functions = [
        'producto' => 'add_product',
        'variante' => 'select_variante',
        'combo' => 'select_combo'
    ];

    echo json_encode([
        'success' => true,
        'data' => $data,
        'extras' => $extras,
        'variants' => $variants,
        'functions' => $functions,
        'combos' => $combos,
        'hora' => date('H:i:s')
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al consultar datos']);
}
?>