<?php
include('../model/db.php');
$list_materias = materias('all');

header('Content-Type: application/json');
echo json_encode(array_map(fn($m) => [
    'id'     => $m['id'],
    'nombre' => $m['materia']
], $list_materias));
?>