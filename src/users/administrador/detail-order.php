<?php
include('../../model/db.php');
$order = $_POST['order'];

$data_order = data_order($order);
$data_items = order_items($order);
$data_pays = data_pays($order);
$data_offer = data_offers($order);

// Agrupar los items por batch (seq)
$batches = [];
$order_total = 0; // total general de la comanda

if ($data_items) {
    foreach ($data_items as $item) {
        $seq = $item['seq'];
        if (!isset($batches[$seq])) {
            $batches[$seq] = [
                'added_at' => $item['added_at'],
                'items' => []
            ];
        }
        $batches[$seq]['items'][] = $item;
    }
    ksort($batches); // ordena los batches por número de seq
}
?>

<div class="d-grid gap-2 text-center">
    <div>
        <b class="fs-3">Comanda #<?= $order ?></b>
        <div>
            <span><?= $data_order['delivery'] == 'mesa' ? '<b class="text-primary me-2">Mesa ' . $data_order['n_table'] . '</b><b class="text-secondary">' . $data_order['client'] . '</b>' : '<b class="text-secondary">' . $data_order['client'] . '</b>' ?></span>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-evenly fw-bold">
        <span><?= $data_order['name'] ?></span>
        <span><?= $data_order['created_at'] ?></span>
    </div>
</div>
<div class="bg-dark mt-3 mb-1" style="height: 3px;"></div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 pt-2">
            <div class="text-center text-light bg-dark p-2 rounded" style="font-size: 0.85rem;">
                <b>Productos de la Comanda</b>
            </div>
            <?php
            if (!empty($batches)) {
                foreach ($batches as $seq => $batch) {
                    ?>
                    <div class="d-flex align-items-center justify-content-between mt-3 mb-1 px-1">
                        <span class="fw-bold text-primary"><?= $seq == 1 ? 'Comanda creada' : 'Agregado despues' ?></span>
                        <span class="text-muted" style="font-size: 0.8rem;"><?= $batch['added_at'] ?></span>
                    </div>
                    <?php
                    foreach ($batch['items'] as $item) {
                        $data_extras = item_extras($item['id']);

                        // Sumar el total de los extras (por unidad)
                        $extras_total = 0;
                        if ($data_extras) {
                            foreach ($data_extras as $extra) {
                                $extras_total += $extra['total'];
                            }
                        }

                        // Total unitario del item (precio + extras)
                        $item_unit_total = $item['price_unit'] + $extras_total;

                        // Total final = unitario x cantidad pedida
                        $item_final_total = $item_unit_total * $item['qty'];

                        // Acumular al total general de la comanda
                        $order_total += $item_final_total;

                        // Si el item es combo, traer los productos seleccionados
                        $data_combo_items = $item['type'] == 'combo' ? combo_selected_items($item['id']) : false;
                        ?>
                        <div class="border border-dark border-opacity-50 border-2 rounded p-2 d-grid gap-2 mt-2">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <?= $item['type'] == 'combo' ? '<b class="me-2 bg-danger text-light p-2 pt-1 pb-1 rounded"><i class="fi fi-br-hamburger-soda"></i></b>' : ($item['type'] == 'especial' ? '<b class="me-2 bg-info p-2 pt-1 pb-1 rounded"><i class="fi fi-br-crown"></i></b>' : '') ?><b><?= $item['name'] ?></b><i
                                        class="ms-1">($<?= number_format($item['price_unit'], 2) ?>)</i>
                                </div>
                                <div>
                                    <div class="border border-dark border-2 bg-warning text-dark rounded d-flex align-items-center justify-content-center fw-bold"
                                        style="height: 28px; width: 28px;"><?= $item['qty'] ?></div>
                                </div>
                            </div>
                            <?php
                            if ($data_combo_items) {
                                echo '<div class="p-2" style="border: 1px dotted #dc3545; border-radius: 5px;">';
                                $current_group = null;
                                foreach ($data_combo_items as $combo_item) {
                                    if ($current_group !== $combo_item['group_item']) {
                                        $current_group = $combo_item['name_group_item'];
                                        echo '<span class="d-block text-danger fw-bold" style="font-size: 0.75rem;">' . $current_group . '</span>';
                                    }
                                    ?>
                                    <li style="font-size: 0.85rem;"><?= $combo_item['qty'] . ' x ' . $combo_item['name_item'] ?></li>
                                    <?php
                                }
                                echo '</div>';
                            }

                            if ($data_extras) {
                                echo '<div class="p-2" style="border: 1px dotted #000000; border-radius: 5px;">';
                                foreach ($data_extras as $extra) {
                                    ?>
                                    <li style="font-size: 0.85rem;"><b><?= $extra['qty'] . ' x ' . $extra['name'] ?></b><i
                                            class="ms-1">($<?= number_format($extra['total'], 2) ?>)</i></li>
                                    <?php
                                }
                                echo '</div>';
                            }
                            if (!empty($item['note'])) {
                                ?>
                                <div class="border border-primary border-opacity-50 p-2 rounded">
                                    <p class="m-0" align="justify"><i style="font-size: 0.85rem;">"<?= $item['note'] ?>"</i></p>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="d-flex align-items-center justify-content-between px-1" style="font-size: 0.85rem;">
                                <span>Total c/extras: <b>$<?= number_format($item_unit_total, 2) ?></b></span>
                                <span>Total (x<?= $item['qty'] ?>): <b>$<?= number_format($item_final_total, 2) ?></b></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between px-1" style="font-size: 0.85rem;">
                                <div>
                                    <?php
                                    if ($data_order['status'] == 'pendiente') {
                                        ?>
                                        <button
                                            class="<?= $item['realized'] == 1 || $item['finished'] !== null ? 'bg-success text-light' : 'bg-warning' ?>"
                                            style="padding: 6px;" onclick="eliminar_item(<?= $order . ',' . $item['id'] ?>)"><i
                                                class="fi fi-br-trash"></i></button>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <span><?= $item['payed'] > 0 ? '<b class="bg-warning p-1 ps-2 pe-2 rounded" style="font-size: 0.70rem;">Pagado: ' . $item['payed'] . '</b>' : '' ?>
                                    <?= $item['payed'] > 0 && $item['payed'] < $item['qty'] ? '<b class="bg-secondary text-white p-1 ps-2 pe-2 rounded" style="font-size: 0.70rem;">Falta: $' . number_format($item_unit_total * (($item['qty'] - $item['payed'])), 2) . '</b>' : '' ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <div class="d-flex align-items-center justify-content-between mt-3 p-2 bg-secondary text-light rounded">
                    <span class="fw-bold" style="font-size: 0.85rem;">Total de la Comanda</span>
                    <span class="fw-bold">$<?= number_format($order_total, 2) ?></span>
                </div>
                <?php
                if ($data_order['paid'] > 0) {
                    ?>
                    <div class="d-flex align-items-center justify-content-between mt-1 p-2 border border-dark border-2 rounded">
                        <span class="fw-bold" style="font-size: 0.85rem;">Pagado</span>
                        <span class="fw-bold">$
                            <?= number_format($data_order['paid'], 2) ?>
                        </span>
                    </div>
                    <?php
                }
                if ($data_order['debt'] > 0) {
                    ?>
                    <div class="d-flex align-items-center justify-content-between mt-1 p-2 border border-dark border-2 rounded">
                        <span class="fw-bold" style="font-size: 0.85rem;">A pagar</span>
                        <span class="fw-bold">$
                            <?= number_format($data_order['debt'], 2) ?>
                        </span>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="p-2 mt-2 border border-2 rounded text-center">No hay productos</div>';
            }
            ?>
        </div>

        <div class="col-12 col-md-6 pt-2">
            <div class="border border-dark border-2">
                <div class="p-2 text-center bg-dark text-light" style="font-size: 0.85rem;">
                    <b>Observaciones</b>
                </div>
                <div class="p-2">
                    <?php
                    if ($data_order['status'] == 'pendiente') {
                        ?>
                        <form method="post" action="" id="cancel-form">
                            <span>Al cancelar la comanda:</span>
                            <li>No se contará como pendiente en cocina/barra.</li>
                            <li>No aparecerá como pendiente de cobro.</li>
                            <li>El motivo quedará registrado en la bitácora de cancelaciones.</li>
                            <div class="d-grid mt-2">
                                <label>Motivo de cancelación</label>
                                <textarea name="motivo-cancel"></textarea>

                                <input type="hidden" name="order-to-cancel" value="<?= $order ?>">
                                <input type="hidden" name="request" value="order-cancel">
                            </div>
                            <div class="d-grid mt-2">
                                <button type="submit" class="btn-execute">Cancelar</button>
                            </div>
                        </form>
                        <?php
                    } else if ($data_order['status'] == 'cancelado') {
                        ?>
                            <b class="text-danger">Comanda cancelada!</b>
                            <div class="mt-2">
                                <span class="text-muted">Motivo:</span>
                                <p class="m-0" align="justify"><?= $data_order['note'] ?></p>
                            </div>
                        <?php
                    } else if ($data_order['status'] == 'finalizado') {
                        ?>
                                <b class="text-success">Comanda completada!</b>
                                <li>La comanda fue cobrada.</li>
                                <li>Los productos fueron entregados.</li>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="border border-dark border-2 mt-2">
                <div class="p-2 text-center bg-dark text-light" style="font-size: 0.85rem;">
                    <b>Descuentos</b>
                </div>
                <div class="p-2">
                    <?php
                    if ($data_order['status'] == 'pendiente') {
                        if ($data_offer) {
                            foreach ($data_offer as $offer) {
                                ?>
                                <div class="border border-2" style="font-size: 0.85rem;">
                                    <div class="d-flex align-items-center justify-content-between p-2 gap-2">
                                        <span>
                                            <?= $offer['type'] == 'porcentaje' ? '<b class="text-primary">' . $offer['value'] . '%</b> ($' . $offer['value_calculado'] . ')' : '$' . $offer['value'] ?>
                                        </span>
                                        <i class="text-muted">
                                            <?= $offer['date'] ?>
                                        </i>
                                        <button class="bg-danger text-white"
                                            onclick="delete_offer(<?= $order . ',' . $offer['id'] ?>)"><i
                                                class="fi fi-br-trash"></i></button>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <span>Detalles de los descuentos:</span>
                            <li>Se calcula con base al total actual de la comanda.</li>

                            <form method="post" action="" id="applyoffer-form">
                                <div class="d-grid mt-2">
                                    <label>Tipo de descuento</label>
                                    <select name="type-offer">
                                        <option value="porcentaje">Porcentaje</option>
                                        <option value="fijo">Monto Fijo</option>
                                    </select>

                                    <label class="mt-2">Valor del descuento</label>
                                    <input type="number" name="value-offer" step="0.01" min="0">

                                    <label class="mt-2">Motivo de descuento</label>
                                    <textarea name="motivo-offer"></textarea>

                                    <input type="hidden" name="order-to-offer" value="<?= $order ?>">
                                    <input type="hidden" name="request" value="order-offer">
                                </div>
                                <div class="d-grid mt-2">
                                    <button type="submit" class="btn-execute">Aplicar Descuento</button>
                                </div>
                            </form>
                            <?php
                        }
                    } else if ($data_order['status'] == 'cancelado' || $data_order['status'] == 'finalizado') {
                        if ($data_offer) {
                            foreach ($data_offer as $offer) {
                                ?>
                                    <div class="border border-2" style="font-size: 0.85rem;">
                                        <div class="d-flex align-items-center justify-content-between p-2 gap-2">
                                            <span>
                                            <?= $offer['type'] == 'porcentaje' ? '<b class="text-primary">' . $offer['value'] . '%</b> ($' . $offer['value_calculado'] . ')' : '$' . $offer['value'] ?>
                                            </span>
                                            <i class="text-muted">
                                            <?= $offer['date'] ?>
                                            </i>
                                        </div>
                                    </div>
                                <?php
                            }
                        } else {
                            echo '<div class="p-2 border border-2 rounded text-center">No descuentos aplicados</div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="border border-dark border-2 mt-2">
                <div class="p-2 text-center bg-dark text-light" style="font-size: 0.85rem;">
                    <b>Pagos</b>
                </div>
                <div class="d-grid gap-2 p-2">
                    <?php
                    if ($data_pays) {
                        foreach ($data_pays as $pay) {
                            if ($pay['method'] == 'efectivo') {
                                $bg_color = 'bg-success text-light';
                            } else if ($pay['method'] == 'tarjeta') {
                                $bg_color = 'bg-danger text-light';
                            } else if ($pay['method'] == 'transferencia') {
                                $bg_color = 'bg-primary text-light';
                            } else if ($pay['method'] == 'mixto') {
                                $bg_color = 'bg-warning';
                            }
                            ?>
                            <div class="border border-2" style="font-size: 0.85rem;">
                                <div class="fw-bold p-1 text-capitalize text-center <?= $bg_color ?>"><?= $pay['method'] ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 gap-2">
                                    <?php
                                    if ($pay['method'] == 'mixto') {
                                        ?>
                                        <div>
                                            <div><b class="text-success">Efectivo</b></div>
                                            <b>$
                                                <?= number_format($pay['received_one'], 2) ?>
                                            </b>
                                        </div>
                                        <div>
                                            <div><b class="text-danger">Tarjeta</b></div>
                                            <b>$
                                                <?= number_format($pay['received_two'], 2) ?>
                                            </b>
                                        </div>
                                        <div>
                                            <i class="text-muted">
                                                <?= $pay['date'] ?>
                                            </i>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <b>$
                                            <?= number_format($pay['neto_cuenta'], 2) ?>
                                        </b>
                                        <i class="text-muted">
                                            <?= $pay['date'] ?>
                                        </i>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="p-2 border border-2 rounded text-center">No hay pagos</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $('#cancel-form').submit(function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: 'crud-order.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                show_load();
            },
            success: function (response) {
                if (response.status === 201) {
                    show_alert(response.title, response.message);
                    detail_order(response.order);
                    reload_datatableOrders();
                } else {
                    show_alert(response.title, response.message);
                }
            },
            //Manejo de error del servidor
            error: function (xhr, status, error) {
                show_alert('ERROR', 'Error al realizar operacion, Intente de nuevo');
            },
            complete: function () {
                hide_load();
            }
        });
    });

    $('#applyoffer-form').submit(function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: 'crud-order.php',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
                show_load();
            },
            success: function (response) {
                if (response.status === 201) {
                    show_alert(response.title, response.message);
                    detail_order(response.order);
                } else {
                    show_alert(response.title, response.message);
                }
            },
            //Manejo de error del servidor
            error: function (xhr, status, error) {
                show_alert('ERROR', 'Error al realizar operacion, Intente de nuevo');
            },
            complete: function () {
                hide_load();
            }
        });
    });
</script>