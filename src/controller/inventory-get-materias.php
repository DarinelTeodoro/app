<?php
header('Content-Type: application/json');
include('../model/db.php');
$list_materias = materias('all');

if ($list_materias === false) {
    echo json_encode([]);
    exit;
}

echo json_encode(array_map(fn($m) => [
    'id'     => $m['id'],
    'nombre' => $m['materia']
], $list_materias));
?>