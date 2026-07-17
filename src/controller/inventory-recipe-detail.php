<?php
include('../model/db.php');
$id = $_POST['item'];
$type = $_POST['type'];

#echo "ID recibido: $id <br>";
#echo "Type recibido: $type <br>";

$list_materias = recipe($id, $type);

# echo "<pre>";
# var_dump($list_materias);
# echo "</pre>";

if ($list_materias) {
    echo '<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="align-middle text-start">Ingrediente / Materia</th>
                            <th class="align-middle text-center">Cantidad</th>
                            <th class="align-middle text-end"></th>
                        </tr>
                    </thead>
                    <tbody>';
    foreach ($list_materias as $m) {
        if ($m['metric'] == 'mililitros') {
            $metric = 'ml';
        } else if ($m['metric'] == 'gramos') {
            $metric = 'gr';
        } else if ($m['metric'] == 'unidades') {
            $metric = 'pz';
        }

        $discount = (int) $m['value'] . ' ' . $metric;
        ?>
        <tr>
            <td class="align-middle text-start"><?= $m['name_materia'] ?></td>
            <td class="align-middle text-center"><?= $discount ?></td>
            <td class="align-middle text-end"><button type="button" class="btn-delete" onclick="delete_recipe(<?= $id ?>, '<?= $type ?>', <?= $m['materia'] ?>)"><i class="fi fi-br-trash"></i></button></td>
        </tr>
        <?php
    }
    echo '</tbody>
            </table>
        </div>';
} else {
    echo '<div class="container-system-message">
            <i class="fi fi-br-recipe-book"></i>
            <span>Aun no se ha configurado la receta</span>
        </div>';
}
?>