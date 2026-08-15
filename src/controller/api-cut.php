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


$sql_orders = $conexion->prepare("SELECT DISTINCT(orden), waiter_name FROM view_waiters WHERE date >= :date_init AND date < :date_finish ORDER BY orden");
$sql_orders->bindParam(':date_init', $date_init);
$sql_orders->bindParam(':date_finish', $date_finish);
$sql_orders->execute();
$orders = $sql_orders->fetchAll(PDO::FETCH_ASSOC);


$sql_cuts = $conexion->prepare("SELECT * FROM cut_checkout WHERE fecha >= :date_init AND fecha < :date_finish ORDER BY fecha ASC");
$sql_cuts->bindParam(':date_init', $date_init);
$sql_cuts->bindParam(':date_finish', $date_finish);
$sql_cuts->execute();
$cuts = $sql_cuts->fetchAll(PDO::FETCH_ASSOC);

$sql_adjustments = $conexion->prepare("SELECT * FROM adjustment_checkout WHERE used = 1 AND fecha >= :date_init AND fecha < :date_finish ORDER BY fecha ASC");
$sql_adjustments->bindParam(':date_init', $date_init);
$sql_adjustments->bindParam(':date_finish', $date_finish);
$sql_adjustments->execute();
$adjustments = $sql_adjustments->fetchAll(PDO::FETCH_ASSOC);

$sql_movimientos = $conexion->prepare("SELECT * FROM view_movimientos WHERE fecha_movimiento >= :date_init AND fecha_movimiento < :date_finish ORDER BY fecha_movimiento ASC, movimiento ASC");
$sql_movimientos->bindParam(':date_init', $date_init);
$sql_movimientos->bindParam(':date_finish', $date_finish);
$sql_movimientos->execute();
$movimientos = $sql_movimientos->fetchAll(PDO::FETCH_ASSOC);
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

<div class="container-fluid">
    <div class="row">
        <div class="col-12 pt-2 pb-2">
            <div class="bg-secondary text-center text-light p-2" style="font-size: 0.85rem;"><b>Movimientos de Caja</b>
            </div>
            <div class="bg-light p-2">
                <div class="text-muted mb-1 fw-bold" style="font-size: 0.85rem;">Tabla de Ventas</div>
                <div class="table-responsive">
                    <table class="table border border-dark" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th class="text-center">Folio</th>
                                <th>Cliente</th>
                                <th>Cuenta + Propina = Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <?php
                            if ($orders) {
                                foreach ($orders as $o) {
                                    $data_pays = data_pays_order($o['orden'], $date_init, $date_finish);

                                    // Verificar si hay al menos un pago en efectivo o mixto
                                    $tiene_pago_relevante = false;
                                    if ($data_pays) {
                                        foreach ($data_pays as $pay) {
                                            if ($pay['method'] == 'efectivo' || $pay['method'] == 'mixto') {
                                                $tiene_pago_relevante = true;
                                                break;
                                            }
                                        }
                                    }

                                    // Si no tiene pagos en efectivo/mixto, saltar esta orden
                                    if (!$tiene_pago_relevante) {
                                        continue;
                                    }

                                    $data = data_order($o['orden']);
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $o['orden'] ?></td>
                                        <td class="align-middle">
                                            <?= $data['delivery'] == 'mesa' ? '<b class="text-secondary me-2">Mesa ' . $data['n_table'] . '</b><span>' . $data['client'] . '</span>' : $data['client'] ?>
                                        </td>
                                        <td class="align-middle">
                                            <?php
                                            foreach ($data_pays as $pay) {
                                                if ($pay['method'] == 'mixto') {
                                                    echo '<div class="p-1 rounded" style="background: #e5e5e5;">
                                                        <div class="text-center"><b>Mixto</b></div>
                                                        <div>
                                                            <li><b class="text-success">Efectivo:</b> $' . $pay['received_one'] . ' <b>+</b> $' . $pay['tip_one'] . ' <b>=</b> $' . number_format(($pay['received_one'] + $pay['tip_one']), 2) . '</li>
                                                        </div>
                                                    </div>';
                                                } else if ($pay['method'] == 'efectivo') {
                                                    echo '<div class="mt-1 mb-1">
                                                        <li class="text-capitalize"><b class="text-success">' . $pay['method'] . ':</b> $' . $pay['neto_cuenta'] . ' <b>+</b> $' . $pay['tip_one'] . ' <b>=</b> $' . number_format(($pay['neto_cuenta'] + $pay['tip_one']), 2) . '</li>
                                                        <div><span class="me-1">Recibido:</span><b>$' . number_format($pay['received_one'], 2) . '</b>' . ($pay['sobrante'] > 0 ? '<span class="ms-2 me-1">Cambio:</span> <b>$' . number_format($pay['sobrante'], 2) . '</b>' : '') . '</div>
                                                    </div>';
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
                                    <td class="text-center fw-bold" colspan="3">No hay pagos registrados</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        </tbody>
                    </table>
                </div>

                <div class="text-muted mb-1 mt-2 fw-bold" style="font-size: 0.85rem;">Tabla de Ajustes</div>
                <div class="table-responsive">
                    <table class="table border border-dark" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th class="text-center">cantidad</th>
                                <th>usuario</th>
                                <th class="text-center">fecha</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <?php
                            if ($adjustments) {
                                foreach ($adjustments as $adj) {
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <?= $adj['tipo'] = 'resta' ? '<b class="text-danger">- $' . $adj['cantidad'] . '</b>' : '<b class="text-success">+ $' . $adj['cantidad'] . '</b>' ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= $adj['user'] ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?= $adj['fecha'] ?>
                                        </td>
                                        <td class="align-middle">
                                            <?= $adj['motivo'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td class="text-center fw-bold" colspan="4">No se agregaron ajustes.</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        </tbody>
                    </table>
                </div>
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
                            <td class="bg-secondary text-center text-light" colspan="5"><b>Cortes de Caja</b></td>
                        </tr>
                        <tr>
                            <th class="text-start">Usuario</th>
                            <th class="text-center">Monto Ingresado</th>
                            <th class="text-center">Cantidad Real</th>
                            <th class="text-center">Resultado</th>
                            <th class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($cuts) {
                            foreach ($cuts as $c) {
                                $user = search_userid($c['user']);
                                ?>
                                <tr>
                                    <td class="align-middle"><?= $user['name'] ?></td>
                                    <td class="align-middle text-center">
                                        $<?= number_format($c['cantidad_ingresado'], 2) ?>
                                    </td>
                                    <td class="align-middle text-center">$<?= number_format($c['cantidad_real'], 2) ?></td>
                                    <td class="align-middle text-justify">
                                        <?= $c['resultado'] == 'sobro' ? '<span>Hubo <b class="text-success">$' . number_format($c['diferencia'], 2) . '</b> de mas en caja.</span>' : ($c['resultado'] == 'falto' ? '<span>Hubo un faltante de <b class="text-danger">$' . number_format($c['diferencia'], 2) . '</b> en caja.</span>' : '<span>La cantidad contada en caja es la correcta.</span>') ?>
                                    </td>
                                    <td class="align-middle text-center"><?= $c['fecha'] ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td class="text-center fw-bold" colspan="7">No se han realizado cortes de caja</td>
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
            <div class="bg-secondary text-center text-light p-2" style="font-size: 0.85rem;"><b>Reporte de
                    Movimientos</b>
            </div>
            <div class="bg-light p-2 d-grid gap-2">
                <?php
                if ($movimientos) {
                    foreach ($movimientos as $mov) {
                        if ($mov['movimiento'] == 'inicial') {
                            echo '<div class="bg-warning border border-1 border-dark border-opacity-50 p-2 rounded d-flex align-items-center justify-content-between">
                                <div><b>Monto Inicial</b></div>
                                <div><b>' . $mov['fecha_movimiento'] . '</b></div>
                                <div><b>$' . number_format($mov['cantidad'], 2) . '</b></div>
                            </div>';
                        } else if ($mov['movimiento'] == 'corte') {
                            echo '<div class="bg-danger text-light border border-1 border-dark border-opacity-50 p-2 rounded d-flex align-items-center justify-content-between">
                                <div><b>Corte de Caja</b></div>
                                <div><b>'.$mov['fecha_movimiento'].'</b></div>
                                <div><b>$'.number_format($mov['cantidad'], 2).'</b></div>
                            </div>';
                        } else {
                            echo '<div class="border border-1 border-dark border-opacity-50 p-2 rounded d-flex align-items-center justify-content-between">
                                <div><b>' . ($mov['movimiento'] == 'venta' ? 'Ingreso por orden' : 'Ajuste de caja') . '</b></div>
                                <div>' . (($mov['movimiento'] == 'venta' || $mov['movimiento'] == 'suma') ? '<b class="text-success">+ $' . number_format(($mov['cantidad'] + $mov['propina']), 2) . '</b>' : '<b class="text-danger">- $' . number_format($mov['cantidad'], 2) . '</b>') . '</div>
                            </div>';
                        }
                    }
                } else {
                    echo '<div class="text-center border border-2 border-dark border-opacity-50 p-2 rounded">No hay movimientos</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>