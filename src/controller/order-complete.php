<?php
include('../model/db.php');
date_default_timezone_set('America/Mexico_City');


$conexion = new Conexion();
$conexion->beginTransaction();

try {
    $order = $_POST['order_id'];
    $destination = $_POST['destination'];
    $fecha = date('Y-m-d H:i:s');

    $update = $conexion->prepare('UPDATE items SET realized = 1 WHERE sale_order = :order AND destination = :destination');
    $update->bindParam(':order', $order);
    $update->bindParam(':destination', $destination);
    $update->execute();

    $update_extras = $conexion->prepare('UPDATE items_extras SET realized = 1 WHERE orden = :order AND destination = :destination');
    $update_extras->bindParam(':order', $order);
    $update_extras->bindParam(':destination', $destination);
    $update_extras->execute();

    $update_combo = $conexion->prepare('UPDATE combo_item_selected SET realized = 1 WHERE id_order = :order AND destination = :destination');
    $update_combo->bindParam(':order', $order);
    $update_combo->bindParam(':destination', $destination);
    $update_combo->execute();

    $update_batch = $conexion->prepare('UPDATE batch SET finished = :fecha WHERE sale_order = :order AND finished IS NULL');
    $update_batch->bindParam(':order', $order);
    $update_batch->bindParam(':fecha', $fecha);
    $update_batch->execute();


    # Ajustar Inventario
    $items = do_recipe_main($order, $destination);

    if ($items) {
        foreach ($items as $i) {
            if ($i['type'] == 'combo') {
                $items_selected = do_recipe_combo($i['id'], $destination);

                if ($items_selected) {
                    foreach ($items_selected as $is) {
                        $to_discount = check_discount($is['forean'], $is['type_item']);

                        if ($to_discount) {
                            foreach ($to_discount as $td) {
                                $value_unit = (float) $td['value'];
                                $value_total = ($value_unit * $is['qty']) * $i['qty'];
                                $qty_total = (int) $is['qty'] * (int) $i['qty'];
                                discount($td['materia'], $value_total);
                                insert_move_inventory('combo_item_selected', $is['id'], $order, $is['item'], $is['forean'], $qty_total, $td['materia'], $value_total);
                            }
                        }
                    }
                }


                $extras = do_recipe_extra($i['id'], $destination);

                if ($extras) {
                    foreach ($extras as $e) {
                        $to_discount_extra = check_discount($e['id_extra'], 'extra');

                        if ($to_discount_extra) {
                            foreach ($to_discount_extra as $tde) {
                                $value_unit_extra = (float) $tde['value'];
                                $value_total_extra = ($value_unit_extra * $e['qty']) * $i['qty'];
                                $qty_total_extras = (int) $e['qty'] * (int) $i['qty'];
                                discount($tde['materia'], $value_total_extra);
                                insert_move_inventory('items_extras', $e['id'], $order, $e['id_item'], $e['id_extra'], $qty_total_extras, $tde['materia'], $value_total_extra);
                            }
                        }
                    }
                }
            } else {
                $to_discount = check_discount($i['product_id'], $i['type']);

                if ($to_discount) {
                    foreach ($to_discount as $td) {
                        $value_unit = (float) $td['value'];
                        $value_total = $value_unit * $i['qty'];

                        $extras = do_recipe_extra($i['id'], $i['destination']);

                        if ($extras) {
                            foreach ($extras as $e) {
                                $to_discount_extra = check_discount($e['id_extra'], 'extra');

                                if ($to_discount_extra) {
                                    foreach ($to_discount_extra as $tde) {
                                        $value_unit_extra = (float) $tde['value'];
                                        $value_total_extra = ($value_unit_extra * $e['qty']) * $i['qty'];
                                        $qty_total_extras = (int) $e['qty'] * (int) $i['qty'];
                                        discount($tde['materia'], $value_total_extra);
                                        insert_move_inventory('items_extras', $e['id'], $order, $e['id_item'], $e['id_extra'], $qty_total_extras, $tde['materia'], $value_total_extra);
                                    }
                                }
                            }
                        }

                        discount($td['materia'], $value_total);
                        insert_move_inventory('items', $i['id'], $order, $i['id'], $i['product_id'], $i['qty'], $td['materia'], $value_total);
                    }
                }
            }
        }
    }

    // Si todo salió bien, confirmamos la transacción
    $conexion->commit();

    $response['status'] = 201;
} catch (Exception $e) {
    $conexion->rollBack();

    $response['status'] = 500;
    $response['title'] = 'Error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>