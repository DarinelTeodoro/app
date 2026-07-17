<?php
include('../model/db.php');
header('Content-Type: application/json');
$data = [];

if ($_POST['request'] == 'info-tables') {
    $detail = settings('tables');
    
    $ln['n_tables'] = $detail['value'];
}

$data = $ln;
echo json_encode($data);
?>