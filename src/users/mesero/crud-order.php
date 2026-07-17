<?php
session_start();
include('../../model/db.php');
date_default_timezone_set('America/Mexico_City');
$user_loged = $_SESSION['data-useractive'];

function insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino)
{
    $insert = $conexion->prepare("INSERT INTO items(batch, sale_order, type, product_id, variant_id, combo_id, extra_id, extra_item, name, qty, price_unit, total, note, added_at, destination) VALUES (:batch, :sale_order, :type, :product_id, :variant_id, :combo_id, :extra_id, :extra_item, :name, :qty, :price_unit, :total, :note, :added_at, :destination)");
    $insert->bindParam(':batch', $id_batchid);
    $insert->bindParam(':sale_order', $id_saleorder);
    $insert->bindParam(':type', $tipo);
    $insert->bindParam(':product_id', $id_producto);
    $insert->bindParam(':variant_id', $id_variante);
    $insert->bindParam(':combo_id', $id_combo);
    $insert->bindParam(':extra_id', $id_extra);
    $insert->bindParam(':extra_item', $extra_item);
    $insert->bindParam(':name', $nombre);
    $insert->bindParam(':qty', $cantidad);
    $insert->bindParam(':price_unit', $precio_base);
    $insert->bindParam(':total', $total);
    $insert->bindParam(':note', $nota);
    $insert->bindParam(':added_at', $fecha);
    $insert->bindParam(':destination', $destino);
    $insert->execute();

    return $conexion->lastInsertId();
}

function insert_product_selected($conexion, $id_combo, $id_item, $cbp_type_item, $cbp_name, $cbp_group_item, $cbp_group_name, $cbp_qty, $cbp_id)
{
    $insert = $conexion->prepare("INSERT INTO combo_item_selected(combo, item, type_item, name_item, group_item, name_group_item, qty, forean) VALUES (:combo, :item, :type_item, :name_item, :group_item, :name_group_item, :qty, :forean)");
    $insert->bindParam(':combo', $id_combo);
    $insert->bindParam(':item', $id_item);
    $insert->bindParam(':type_item', $cbp_type_item);
    $insert->bindParam(':name_item', $cbp_name);
    $insert->bindParam(':group_item', $cbp_group_item);
    $insert->bindParam(':name_group_item', $cbp_group_name);
    $insert->bindParam(':qty', $cbp_qty);
    $insert->bindParam(':forean', $cbp_id);
    $insert->execute();

    return $conexion->lastInsertId();
}




if ($_POST['request'] == 'add-order') {
    $carrito = json_decode($_POST['carrito'], true);

    if ($_POST['type-delivery'] == 'mesa' & $_POST['choose-table'] == 0) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'Seleccione el numero de mesa';
        echo json_encode($response);
        exit;
    } else if ($_POST['type-delivery'] == 'domicilio' & empty($_POST['name-client'])) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'Ingrese la información del cliente';
        echo json_encode($response);
        exit;
    }

    if (empty($carrito)) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'La orden esta vacia, agregue al menos un producto';
        echo json_encode($response);
        exit;
    }

    $table = $_POST['choose-table'];
    $client = $_POST['name-client'];
    $fecha = date('Y-m-d H:i:s');

    $conexion = new Conexion();
    $insert_order = $conexion->prepare("INSERT INTO sale_order(delivery, n_table, client, waiter, status, created_at, modified_at) VALUES (:delivery, :n_table, :client, :waiter, 'pendiente', :created_at, :modified_at)");
    $insert_order->bindParam(':delivery', $_POST['type-delivery']);
    $insert_order->bindParam(':n_table', $_POST['choose-table']);
    $insert_order->bindParam(':client', $_POST['name-client']);
    $insert_order->bindParam(':waiter', $user_loged);
    $insert_order->bindParam(':created_at', $fecha);
    $insert_order->bindParam(':modified_at', $fecha);
    $insert_order->execute();
    $id_saleorder = $conexion->lastInsertId();

    $insert_batch = $conexion->prepare("INSERT INTO batch(sale_order, seq, created) VALUES (:sale_order, 1, :created)");
    $insert_batch->bindParam(':sale_order', $id_saleorder);
    $insert_batch->bindParam(':created', $fecha);
    $insert_batch->execute();
    $id_batchid = $conexion->lastInsertId();


    foreach ($carrito as $item) {
        $tipo = $item['type'];

        if ($tipo == 'producto') {
            $id_producto = $item['id'];
            $id_variante = null;
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        }if ($tipo == 'especial') {
            $id_producto = null;
            $id_variante = null;
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        } else if ($tipo == 'variante') {
            $id_producto = $item['id'];
            $id_variante = $item['variant_id'];
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        } else if ($tipo == 'combo' && !empty($item['comboItems'])) {
            $id_producto = null;
            $id_variante = null;
            $id_combo = $item['id'];
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = 'Ambos';
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);

            foreach ($item['comboItems'] as $ci) {
                $cbp_id = $ci['id'];
                $cbp_name = $ci['name'];
                $cbp_qty = $ci['qty'];
                $cbp_group_item = $ci['group_id'];
                $cbp_group_name = $ci['group_name'];
                $cbp_type_item = $ci['type'];

                $id_selected = insert_product_selected($conexion, $id_combo, $id_item, $cbp_type_item, $cbp_name, $cbp_group_item, $cbp_group_name, $cbp_qty, $cbp_id);
            }
        }

        foreach ($item['extras'] as $extra) {
            $e_tipo = 'extra';
            $e_id_producto = null;
            $e_id_variante = null;
            $e_id_combo = null;
            $e_id_extra = $extra['id'];
            $e_extra_item = $id_item;
            $e_nombre = $extra['extra'];
            $e_destino = $extra['destination'];
            $e_precio_base = $extra['price'];
            $e_cantidad = $extra['qty'];
            $e_nota = null;
            $e_total = (float) $extra['price'] * (int) $extra['qty'];

            insert_item($conexion, $id_batchid, $id_saleorder, $e_tipo, $e_id_producto, $e_id_variante, $e_id_combo, $e_id_extra, $e_extra_item, $e_nombre, $e_cantidad, $e_precio_base, $e_total, $e_nota, $fecha, $e_destino);
        }

    }

    $response['status'] = 201;
    $response['title'] = 'Operación Exitosa';
    $response['message'] = 'La orden fue creada';
    echo json_encode($response);
}




if ($_POST['request'] == 'edit-order') {
    $id_saleorder = $_POST['id-order'];
    $carrito = json_decode($_POST['carrito'], true);

    if (empty($carrito)) {
        $response['status'] = 400;
        $response['title'] = 'Alerta';
        $response['message'] = 'La orden esta vacia, agregue al menos un producto';
        echo json_encode($response);
        exit;
    }

    $fecha = date('Y-m-d H:i:s');

    $conexion = new Conexion();
    $update_order = $conexion->prepare("UPDATE sale_order SET modified_at = :modified_at WHERE id = :order");
    $update_order->bindParam(':order', $id_saleorder);
    $update_order->bindParam(':modified_at', $fecha);
    $update_order->execute();

    $get_batch = $conexion->prepare("SELECT MAX(seq) AS seq FROM batch WHERE sale_order = :sale_order");
    $get_batch->bindParam(':sale_order', $id_saleorder);
    $get_batch->execute();
    $data_batch = $get_batch->fetch();
    $seq = (int) $data_batch['seq'] + 1;

    $insert_batch = $conexion->prepare("INSERT INTO batch(sale_order, seq, created) VALUES (:sale_order, :seq, :created)");
    $insert_batch->bindParam(':sale_order', $id_saleorder);
    $insert_batch->bindParam(':seq', $seq);
    $insert_batch->bindParam(':created', $fecha);
    $insert_batch->execute();
    $id_batchid = $conexion->lastInsertId();


    foreach ($carrito as $item) {
        $tipo = $item['type'];

        if ($tipo == 'producto') {
            $id_producto = $item['id'];
            $id_variante = null;
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        }if ($tipo == 'especial') {
            $id_producto = null;
            $id_variante = null;
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        } else if ($tipo == 'variante') {
            $id_producto = $item['id'];
            $id_variante = $item['variant_id'];
            $id_combo = null;
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = $item['destination'];
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);
        } else if ($tipo == 'combo' && !empty($item['comboItems'])) {
            $id_producto = null;
            $id_variante = null;
            $id_combo = $item['id'];
            $id_extra = null;
            $extra_item = null;
            $nombre = $item['product'];
            $destino = 'Ambos';
            $precio_base = $item['basePrice'];
            $total = (float) $item['basePrice'] * (int) $item['qty'];
            $cantidad = $item['qty'];
            $nota = $item['comment'];

            $id_item = insert_item($conexion, $id_batchid, $id_saleorder, $tipo, $id_producto, $id_variante, $id_combo, $id_extra, $extra_item, $nombre, $cantidad, $precio_base, $total, $nota, $fecha, $destino);

            foreach ($item['comboItems'] as $ci) {
                $cbp_id = $ci['id'];
                $cbp_name = $ci['name'];
                $cbp_qty = $ci['qty'];
                $cbp_group_item = $ci['group_id'];
                $cbp_group_name = $ci['group_name'];
                $cbp_type_item = $ci['type'];

                $id_selected = insert_product_selected($conexion, $id_combo, $id_item, $cbp_type_item, $cbp_name, $cbp_group_item, $cbp_group_name, $cbp_qty, $cbp_id);
            }
        }

        foreach ($item['extras'] as $extra) {
            $e_tipo = 'extra';
            $e_id_producto = null;
            $e_id_variante = null;
            $e_id_combo = null;
            $e_id_extra = $extra['id'];
            $e_extra_item = $id_item;
            $e_nombre = $extra['extra'];
            $e_destino = $extra['destination'];
            $e_precio_base = $extra['price'];
            $e_cantidad = $extra['qty'];
            $e_nota = null;
            $e_total = (float) $extra['price'] * (int) $extra['qty'];

            insert_item($conexion, $id_batchid, $id_saleorder, $e_tipo, $e_id_producto, $e_id_variante, $e_id_combo, $e_id_extra, $e_extra_item, $e_nombre, $e_cantidad, $e_precio_base, $e_total, $e_nota, $fecha, $e_destino);
        }

    }

    $response['status'] = 201;
    $response['title'] = 'Operación Exitosa';
    $response['message'] = 'Se agregaron nuevos productos al orden #'.$id_saleorder;
    echo json_encode($response);
}
?>