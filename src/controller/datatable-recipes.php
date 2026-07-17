<?php
include('../model/db.php');
header('Content-Type: application/json');

try {
    $rows = view_products();
    $data = [];

    if ($rows) {
        foreach ($rows as $row) {
            $ln['group'] =  $row['name_category'];
            $ln['product'] = $row['name'];
            $ln['recipe'] = $row['recipe'] > 0 ? '' : '<i class="fi fi-br-diamond-exclamation text-danger"></i>';
            $ln['recipe_text'] = $row['recipe'] > 0 ? 'Receta Configurada' : 'Sin Receta';
            $ln['actions'] = '<button class="btn-edit mb-1" onclick="details_recipe(' . $row['id'] . ', \''.$row['type'].'\')"><i class="fi fi-br-recipe-book"></i></button>
            <button class="btn-add mt-1" onclick="add_recipe(' . $row['id'] . ', \''.$row['type'].'\')"><i class="fi fi-br-hat-chef"></i></button>';
            $data[] = $ln;
        }
    }

    echo json_encode(['data' => $data]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>