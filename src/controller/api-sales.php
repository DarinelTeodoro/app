<?php
session_start();
include('../model/db.php');
date_default_timezone_set('America/Mexico_City');
$conexion = new Conexion();

function get_pagos($conexion, $orden)
{
    $sql = $conexion->prepare("SELECT * FROM transactions WHERE orden = :orden");
    $sql->bindParam(':orden', $orden);
    $sql->execute();

    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

if (empty($_SESSION['data-useractive'])) {
    exit;
}

$fecha = $_GET['fecha'] ?? null;

if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) {
    exit;
} else {
    $date_init = date('Y-m-d 06:00:00', strtotime($fecha));
    $date_finish = date('Y-m-d 06:00:00', strtotime($fecha . ' +1 day'));
}



$sql_n = $conexion->prepare("SELECT COUNT(DISTINCT orden) AS n FROM transactions WHERE date >= :date_init AND date < :date_finish");
$sql_n->bindParam(':date_init', $date_init);
$sql_n->bindParam(':date_finish', $date_finish);
$sql_n->execute();
$data_n = $sql_n->fetch(PDO::FETCH_ASSOC);


$sql_neto = $conexion->prepare("SELECT SUM(neto_cuenta) AS neto FROM transactions WHERE date >= :date_init AND date < :date_finish");
$sql_neto->bindParam(':date_init', $date_init);
$sql_neto->bindParam(':date_finish', $date_finish);
$sql_neto->execute();
$data_neto = $sql_neto->fetch(PDO::FETCH_ASSOC);
$neto = $data_neto['neto'];


$sql_tip = $conexion->prepare("SELECT SUM(tip_one) AS tip_one, SUM(tip_two) AS tip_two FROM transactions WHERE date >= :date_init AND date < :date_finish");
$sql_tip->bindParam(':date_init', $date_init);
$sql_tip->bindParam(':date_finish', $date_finish);
$sql_tip->execute();
$data_tip = $sql_tip->fetch(PDO::FETCH_ASSOC);
$total_tip = $data_tip['tip_one'] + $data_tip['tip_two'];


//Comandas canceladas
/*$sql = $conexion->prepare("SELECT *
        FROM view_order
        WHERE DATE(created_at) = :fecha
        ORDER BY created_at DESC");
$stmt->bindParam(':fecha', $fecha);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($data) {
    foreach ($data as $d) {
        ?>
        <div><?= $d['id'] ?></div>
        <?php
    }
}*/

function count_pagos($conexion, $method, $date_init, $date_finish)
{
    if ($method == 'transferencia') {
        $stmt = "SELECT COUNT(id) AS n FROM transactions WHERE method = :method AND date >= :date_init AND date < :date_finish";
    } else {
        $stmt = "SELECT COUNT(id) AS n FROM transactions WHERE (method = :method OR method = 'mixto') AND date >= :date_init AND date < :date_finish";
    }

    $sql = $conexion->prepare($stmt);
    $sql->bindParam(':method', $method);
    $sql->bindParam(':date_init', $date_init);
    $sql->bindParam(':date_finish', $date_finish);
    $sql->execute();
    return $sql->fetch(PDO::FETCH_ASSOC);
}

function total_waiter($conexion, $date_init, $date_finish)
{
    $sql = $conexion->prepare("
        SELECT
            COUNT(DISTINCT orden) AS nr,
            waiter_id,
            waiter_name,
            SUM(neto_cuenta) AS base,
            SUM(tip_one) AS propinas_one,
            SUM(tip_two) AS propinas_two
        FROM view_waiters
        WHERE `date` >= :date_init
        AND `date` < :date_finish
        GROUP BY waiter_id
    ");

    $sql->bindParam(':date_init', $date_init);
    $sql->bindParam(':date_finish', $date_finish);

    $sql->execute();

    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

$data_waiters = total_waiter($conexion, $date_init, $date_finish);

function total_method($conexion, $method, $date_init, $date_finish)
{
    $sql = $conexion->prepare("
        SELECT 
            SUM(neto_cuenta) AS total_method,
            SUM(tip_one) AS propina_method
        FROM transactions
        WHERE method = :method
        AND date >= :date_init
        AND date < :date_finish
    ");

    $sql->bindParam(':method', $method);
    $sql->bindParam(':date_init', $date_init);
    $sql->bindParam(':date_finish', $date_finish);
    $sql->execute();

    $r1 = $sql->fetch(PDO::FETCH_ASSOC);

    if ($method == 'tarjeta' || $method == 'efectivo') {

        if ($method == 'tarjeta') {
            $stmt = "
                SELECT 
                    SUM(received_two) AS total_mixto,
                    SUM(tip_two) AS propina_mixto
                FROM transactions
                WHERE method = 'mixto'
                AND date >= :date_init
                AND date < :date_finish
            ";
        } else {
            $stmt = "
                SELECT 
                    SUM(received_one) AS total_mixto,
                    SUM(tip_one) AS propina_mixto
                FROM transactions
                WHERE method = 'mixto'
                AND date >= :date_init
                AND date < :date_finish
            ";
        }

        $sql_2 = $conexion->prepare($stmt);
        $sql_2->bindParam(':date_init', $date_init);
        $sql_2->bindParam(':date_finish', $date_finish);
        $sql_2->execute();

        $r2 = $sql_2->fetch(PDO::FETCH_ASSOC);

        $total_mixto = $r2['total_mixto'] ?? 0;
        $propina_mixto = $r2['propina_mixto'] ?? 0;

    } else {
        $total_mixto = 0;
        $propina_mixto = 0;
    }

    $total_final = (float) $r1['total_method'] + (float) $total_mixto;
    $propina_final = (float) $r1['propina_method'] + (float) $propina_mixto;

    return [
        'total' => $total_final,
        'propina_total' => $propina_final
    ];
}

$n_efec = count_pagos($conexion, 'efectivo', $date_init, $date_finish);
$n_tarj = count_pagos($conexion, 'tarjeta', $date_init, $date_finish);
$n_trans = count_pagos($conexion, 'transferencia', $date_init, $date_finish);

$total_pay_efec = total_method($conexion, 'efectivo', $date_init, $date_finish);
$total_pay_tarj = total_method($conexion, 'tarjeta', $date_init, $date_finish);
$total_pay_trans = total_method($conexion, 'transferencia', $date_init, $date_finish);


$sql_orders = $conexion->prepare("SELECT DISTINCT(orden), waiter_name FROM view_waiters WHERE date >= :date_init AND date < :date_finish ORDER BY orden");
$sql_orders->bindParam(':date_init', $date_init);
$sql_orders->bindParam(':date_finish', $date_finish);
$sql_orders->execute();
$orders = $sql_orders->fetchAll(PDO::FETCH_ASSOC);

$sql_canceled = $conexion->prepare("SELECT DISTINCT(orden), waiter_name FROM view_waiters WHERE status = 'cancelado' AND date_finish >= :date_init AND date_finish < :date_finish");
$sql_canceled->bindParam(':date_init', $date_init);
$sql_canceled->bindParam(':date_finish', $date_finish);
$sql_canceled->execute();
$canceled = $sql_canceled->fetchAll(PDO::FETCH_ASSOC);

$products_sale = $conexion->prepare("SELECT type, name, SUM(total) AS total, SUM(qty) AS qty FROM view_items WHERE finished IS NOT NULL AND finished >= :date_init AND finished < :date_finish GROUP BY product_id, variant_id ORDER BY qty DESC");
$products_sale->bindParam(':date_init', $date_init);
$products_sale->bindParam(':date_finish', $date_finish);
$products_sale->execute();
$rows_ps = $products_sale->fetchAll(PDO::FETCH_ASSOC);

$extras_sale = $conexion->prepare("SELECT name, SUM(qty_extra) AS qty, SUM(total_extra) AS total FROM view_extras WHERE realized = 1 AND finished >= :date_init AND finished < :date_finish GROUP BY id_extra ORDER BY qty DESC");
$extras_sale->bindParam(':date_init', $date_init);
$extras_sale->bindParam(':date_finish', $date_finish);
$extras_sale->execute();
$rows_es = $extras_sale->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="border border-3 d-flex align-items-center justify-content-between rounded mb-2 p-2">
    <div>
        <b>Turno</b>
    </div>
    <div class="d-flex flex-column text-end">
        <span><?= date('d/M/Y', strtotime($fecha)) ?> - 06:00 am</span>
        <span><?= date('d/M/Y', strtotime($fecha . ' +1 day')) ?> - 05:59 am</span>
    </div>
</div>

<div class="container-fluid text-general">
    <div class="row">
        <div class="col-12 col-md-6 col-xl-3 p-2">
            <div class="bg-light p-2 border border-black border-2 rounded">
                <div>
                    <small class="text-muted">COMANDAS COBRADAS</small>
                </div>
                <div>
                    <b class="fs-3 text-primary"><?= $data_n['n'] ?></b>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 p-2">
            <div class="bg-light p-2 border border-black border-2 rounded">
                <div>
                    <small class="text-muted">INGRESO NETO</small>
                </div>
                <div>
                    <b class="fs-3 text-info">$<?= number_format($neto, 2) ?></b>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 p-2">
            <div class="bg-light p-2 border border-black border-2 rounded">
                <div>
                    <small class="text-muted">PROPINAS</small>
                </div>
                <div>
                    <b class="fs-3 text-warning">$<?= number_format($total_tip, 2) ?></b>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3 p-2">
            <div class="bg-light p-2 border border-black border-2 rounded">
                <div>
                    <small class="text-muted">INGRESO TOTAL</small>
                </div>
                <div>
                    <b class="fs-3 text-success">$<?= number_format(($neto + $total_tip), 2) ?></b>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 pt-2 pb-2">
            <div>
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Meseros y Propinas</b></td>
                        </tr>
                        <tr>
                            <th>Mesero</th>
                            <th class="text-center">Comandas</th>
                            <th class="text-center">Ingresos</th>
                            <th class="text-center">Propinas</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($data_waiters) {
                            foreach ($data_waiters as $w) {
                                ?>
                                <tr>
                                    <td><?= $w['waiter_name'] ?></td>
                                    <td class="text-center"><?= $w['nr'] ?></td>
                                    <td class="text-center">$<?= number_format($w['base'], 2) ?></td>
                                    <td class="text-center">$<?= number_format(($w['propinas_one'] + $w['propinas_two']), 2) ?></td>
                                    <td class="text-center">$<?= number_format(($w['base'] + $w['propinas_one'] + $w['propinas_two']), 2) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="5">Sin Regsitros</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 col-md-6 pt-2 pb-2">
            <div>
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Metodos de Pago</b></td>
                        </tr>
                        <tr>
                            <th>Metodo</th>
                            <th class="text-center">Pagos</th>
                            <th class="text-center">Ingresos</th>
                            <th class="text-center">Propinas</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="align-middle">Efectivo</td>
                            <td class="align-middle text-center"><?= $n_efec['n'] ?></td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_efec['total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_efec['propina_total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format(($total_pay_efec['total'] + $total_pay_efec['propina_total']), 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Tarjeta</td>
                            <td class="align-middle text-center"><?= $n_tarj['n'] ?></td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_tarj['total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_tarj['propina_total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format(($total_pay_tarj['total'] + $total_pay_tarj['propina_total']), 2) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Transferencia</td>
                            <td class="align-middle text-center"><?= $n_trans['n'] ?></td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_trans['total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format($total_pay_trans['propina_total'], 2) ?>
                            </td>
                            <td class="align-middle text-center">
                                $<?= number_format(($total_pay_trans['total'] + $total_pay_trans['propina_total']), 2) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




<div class="container-fluid">
    <div class="row">
        <div class="col-12 pt-2 pb-2">
            <div class="table-responsive">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Resumen de Pagos</b></td>
                        </tr>
                        <tr>
                            <th class="text-center">Folio</th>
                            <th>Cliente</th>
                            <th>Mesero</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Pago + Propina = Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($orders) {
                            foreach ($orders as $o) {
                                $data = data_order($o['orden']);

                                $data_pays = data_pays_order($o['orden'], $date_init, $date_finish);
                                ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $o['orden'] ?></td>
                                    <td class="align-middle">
                                        <?= $data['delivery'] == 'mesa' ? '<b class="text-secondary me-2">Mesa ' . $data['n_table'] . '</b><span>' . $data['client'] . '</span>' : $data['client'] ?>
                                    </td>
                                    <td class="align-middle"><?= $o['waiter_name'] ?></td>
                                    <td class="align-middle text-center">$<?= number_format($data['total'], 2) ?></td>
                                    <td class="align-middle">
                                        <?php
                                        if ($data_pays) {
                                            foreach ($data_pays as $pay) {
                                                if ($pay['method'] == 'mixto') {
                                                    echo '<div class="p-1 rounded" style="background: #e5e5e5;">
                                                        <div class="text-center"><b>Mixto</b></div>
                                                        <div>
                                                            <li><b class="text-success">Efectivo:</b> $' . $pay['received_one'] . ' <b>+</b> $' . $pay['tip_one'] . ' <b>=</b> $' . number_format(($pay['received_one'] + $pay['tip_one']), 2) . '</li>
                                                            <li><b class="text-danger">Tarjeta:</b> $' . $pay['received_two'] . ' <b>+</b> $' . $pay['tip_two'] . ' <b>=</b> $' . number_format(($pay['received_two'] + $pay['tip_two']), 2) . '</li>
                                                        </div>
                                                    </div>';
                                                } else {
                                                    echo '<li class="text-capitalize"><b class="' . ($pay['method'] == 'efectivo' ? 'text-success' : ($pay['method'] == 'transferencia' ? 'text-primary' : 'text-danger')) . '">' . $pay['method'] . ':</b> $' . $pay['neto_cuenta'] . ' <b>+</b> $' . $pay['tip_one'] . ' <b>=</b> $' . number_format(($pay['neto_cuenta'] + $pay['tip_one']), 2) . '</li>';
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="7">No hay pagos registrados</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-12 pt-2 pb-2">
            <div class="table-responsive">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Ordenes Canceladas</b></td>
                        </tr>
                        <tr>
                            <th class="text-center">Folio</th>
                            <th>Cliente</th>
                            <th>Mesero</th>
                            <th class="text-center">Cuenta</th>
                            <th class="text-center">Pagado</th>
                            <th class="text-center">Deuda</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($canceled) {
                            foreach ($canceled as $c) {
                                $data = data_order($c['orden']);
                                $data_pays = data_pays_order($c['orden'], $date_init, $date_finish);
                                ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $c['orden'] ?></td>
                                    <td class="align-middle">
                                        <?= $data['delivery'] == 'mesa' ? '<b class="text-secondary me-2">Mesa ' . $data['n_table'] . '</b><span>' . $data['client'] . '</span>' : $data['client'] ?>
                                    </td>
                                    <td class="align-middle"><?= $o['waiter_name'] ?></td>
                                    <td class="align-middle text-center">$<?= number_format($data['total'], 2) ?></td>
                                    <td class="align-middle text-center">$<?= number_format($data['paid'], 2) ?></td>
                                    <td class="align-middle text-center">$<?= number_format($data['debt'], 2) ?></td>
                                    <td class="align-middle text-justify"><?= $data['note'] ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="7">Sin comandas canceladas</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 pt-2 pb-2">
            <div>
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Productos Vendidos</b></td>
                        </tr>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($rows_ps) {
                            foreach ($rows_ps as $ps) {
                                ?>
                                <tr>
                                    <td class="align-middle "><?= $ps['type'] == 'especial' ? '<i class="fi fi-br-crown text-danger me-1"></i><b>Especial</b>' : $ps['name'] ?></td>
                                    <td class="align-middle text-center"><?= $ps['qty'] ?></td>
                                    <td class="align-middle text-center">$<?= number_format($ps['total'], 2) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="3">Sin Regsitros</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 col-md-6 pt-2 pb-2">
            <div>
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <td class="bg-secondary text-center text-light" colspan="7"><b>Extras Vendidos</b></td>
                        </tr>
                        <tr>
                            <th>Extra</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($rows_es) {
                            foreach ($rows_es as $es) {
                                ?>
                                <tr>
                                    <td class="align-middle "><?= $es['name'] ?></td>
                                    <td class="align-middle text-center"><?= $es['qty'] ?></td>
                                    <td class="align-middle text-center">$<?= number_format($es['total'], 2) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="3">Sin Regsitros</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>