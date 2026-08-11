<?php
header('Content-Type: application/json; charset=utf-8');
include('../../model/db.php');

/**
 * Agrupa las filas de combo_item_selected por su grupo (group_item),
 * marcando is_extra cuando type_item = 'extra' dentro del combo.
 */
function build_combo_groups($selected)
{
    if (!$selected)
        return [];

    $grupos = [];
    foreach ($selected as $row) {
        $gid = $row['group_item'];
        if (!isset($grupos[$gid])) {
            $grupos[$gid] = [
                'group_id'   => $gid,
                'group_name' => $row['name_group_item'],
                'items'      => []
            ];
        }
        $grupos[$gid]['items'][] = [
            'name'     => $row['name_item'],
            'qty'      => (int) $row['qty'],
            'is_extra' => $row['type_item'] === 'extra'
        ];
    }
    return array_values($grupos);
}

/**
 * Arma bloques por producto principal / combo.
 * Los extras se obtienen desde su propia tabla via item_extras($id).
 */
function build_item_blocks($items)
{
    if (!$items)
        return [];

    $resultado = [];
    foreach ($items as $item) {

        $bloque = [
            'id'            => (int) $item['id'],
            'name'          => $item['name'],
            'price'          => (float) $item['price_unit'],
            'qty'           => (int) $item['qty'],
            'note'          => $item['note'],
            'type'          => $item['type'],
            'pagado'        => (int) ($item['payed'] ?? 0),
            'batch_id'      => $item['batch'] !== null ? (int) $item['batch'] : null,
            'batch_seq'     => $item['seq']     ?? null,
            'batch_created' => $item['created'] ?? null,
        ];

        // ✅ Extras desde su propia tabla, relacionados por id_item
        $extras_raw = item_extras($item['id']) ?: [];
        $bloque['extras'] = array_map(function ($e) {
            return [
                'id'   => (int) $e['id'],
                'name' => $e['name'],
                'price' => $e['total'],
                'qty'  => (int) $e['qty']
            ];
        }, $extras_raw);

        if ($item['type'] === 'combo') {
            $bloque['groups'] = build_combo_groups(combo_selected_items($item['id']));
        }

        $resultado[] = $bloque;
    }
    return $resultado;
}

/**
 * Agrupa los bloques de items por batch_id, ordenados por seq ascendente.
 */
function build_batches($items)
{
    $blocks = build_item_blocks($items);

    $batches = [];
    foreach ($blocks as $block) {
        $bid = $block['batch_id'];
        if (!isset($batches[$bid])) {
            $batches[$bid] = [
                'batch_id'         => $bid,
                'batch_seq'        => $block['batch_seq'],
                'batch_created_at' => $block['batch_created'],
                'items'            => []
            ];
        }

        unset($block['batch_id'], $block['batch_seq'], $block['batch_created']);
        $batches[$bid]['items'][] = $block;
    }

    $resultado = array_values($batches);
    usort($resultado, function ($a, $b) {
        return ($a['batch_seq'] ?? 0) <=> ($b['batch_seq'] ?? 0);
    });

    return $resultado;
}

try {
    $order = $_GET['order'] ?? null;
    if (!$order) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Falta el parámetro order']);
        exit;
    }

    $items = order_items($order);
    $data  = build_batches($items);

    echo json_encode([
        'success' => true,
        'data'    => $data
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e]);
}