<?php
include('../model/db.php');
$id = $_POST['movement'];
$info_mov = adjustment($id);
$list_details = purchases($id);

if ($list_details) {
    echo '<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="align-middle text-start">Ingrediente / Materia</th>
                            <th class="align-middle text-center">Paquetes</th>
                            <th class="align-middle text-center">Unidades</th>
                            <th class="align-middle text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>';

                    $sign = $info_mov['adjustment'] == 'less' ? '<span class="text-danger fw-bold">-</span> ' : '<span class="text-success fw-bold">+</span> ';

    foreach ($list_details as $d) {
        $info_materia = materia($d['materia']);
        $metric = $info_materia['metric'];

        if ($metric == 'gramos') {
            $value = $d['amount'] / 1000;
            $unit = 'kg';
        } elseif ($metric == 'mililitros') {
            $value = $d['amount'] / 1000;
            $unit = 'lt';
        } else {
            $value = (int) $d['amount'];
            $unit = 'pz';
        }
        ?>
        <tr>
            <td class="align-middle text-start"><?= $d['item'] ?></td>
            <td class="align-middle text-center"><?= $d['qty'] ?></td>
            <td class="align-middle text-center"><?= (int) $d['units'] ?></td>
            <td class="align-middle text-center"><?= $sign.$value . ' ' . $unit ?></td>
        </tr>
        <?php
    }
    echo '</tbody>
            </table>
        </div>';
} else {
    echo '<div class="container-system-message">
            <i class="fi fi-br-file-circle-info"></i>
            <span>No se pudo obtener la información</span>
        </div>';
}
?>