<?php
include('../model/db.php');
header('Content-Type: application/json');

$materia = $_POST['materia'];
$detail = materia($materia);
$data = [];

if ($detail['metric'] == 'mililitros') {
    $ln['metric'] = 'ml';
} else if ($detail['metric'] == 'gramos') {
    $ln['metric'] = 'gr';
} else if ($detail['metric'] == 'unidades') {
    $ln['metric'] = 'pz';
}

$data = $ln;

echo json_encode($data);
?>