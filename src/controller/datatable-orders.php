<?php
include('../model/db.php');
header('Content-Type: application/json');

try {
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM view_order
            ORDER BY id DESC");
    $query->execute();
    $rows =  $query->fetchAll(PDO::FETCH_ASSOC);

    $data = [];

    if ($rows) {
        foreach ($rows as $row) {
            $ln['fecha'] = date('d-m-Y', strtotime($row['created_at']));
            $ln['id'] = 'Orden #'.$row['id'];
            $ln['status'] = $row['status'] == 'finalizado' ? '<b class="text-success">Finalizado</b>' : ($row['status'] == 'cancelado' ? '<b class="text-danger">Cancelado</b>' : '<b class="text-warning">Pendiente</b>');
            $ln['datetime'] = date('H:i', strtotime($row['created_at']));
            $ln['details'] = ($row['barra'] == 2 ? '<span class="border border-success border-2 bg-success text-white p-2 pt-1 pb-1 rounded"><i class="fi fi-br-martini-glass-citrus"></i></span>' : ($row['barra'] == 1 ? '<span class="border border-secondary border-2 p-2 pt-1 pb-1 rounded"><i class="fi fi-br-martini-glass-citrus"></i></span>' : '')) . ($row['cocina'] == 2 ? '<span class="border border-success border-2 bg-success text-white ms-1 p-2 pt-1 pb-1 rounded"><i class="fi fi-br-restaurant"></i></span>' : ($row['cocina'] == 1 ? '<span class="border border-secondary border-2 ms-1 p-2 pt-1 pb-1 rounded"><i class="fi fi-br-restaurant"></i></span>' : '')) . ($row['barra'] == 0 && $row['cocina'] == 0 ? 'Sin Productos' : '');
            $ln['btn'] = '<button class="btn-execute" onclick="detail_order('.$row['id'].')">Detalles</button>';
            $data[] = $ln;
        }
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>