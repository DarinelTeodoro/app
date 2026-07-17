<?php
include('../model/db.php');

function insert_product($conexion, $group, $item_one, $item_two, $type_product)
{
    if ($type_product == 'producto') {
        $product = $item_one;
        $variant = null;
        $extra = null;
    } else if ($type_product == 'variante') {
        $product = $item_one;
        $variant = $item_two;
        $extra = null;
    } else if ($type_product == 'extra') {
        $product = null;
        $variant = null;
        $extra = $item_one;
    }

    $insert = $conexion->prepare('INSERT INTO combo_groups_products(combo_group, product, variant, extra, type) VALUES (:group, :item, :variant, :extra, :type)');
    $insert->bindParam(':group', $group);
    $insert->bindParam(':item', $product);
    $insert->bindParam(':variant', $variant);
    $insert->bindParam(':extra', $extra);
    $insert->bindParam(':type', $type_product);
    $insert->execute();
}

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-combo'];
        $description = $_POST['description-combo'];
        $price = $_POST['price-combo'];

        // Validar datos obligatorios
        if (empty($name) || empty($price)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO combo(combo, description, price) VALUES (:combo, :description, :price)');
        $insert->bindParam(':combo', $name);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':price', $price);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Combo';
        $response['message'] = 'Se agrego el combo "' . $name . '"';
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'delete') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $id_combo = $_POST['combo'];

        $delete = $conexion->prepare('DELETE FROM combo WHERE id = :id');
        $delete->bindParam(':id', $id_combo);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Categoria Eliminada';
        $response['message'] = 'El combo ha sido eliminado de la lista';
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


if ($_POST['request'] == 'edit') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $comboid = $_POST['id-combo-edit'];
        $combo = $_POST['name-combo-edit'];
        $description = $_POST['description-combo-edit'];
        $price = $_POST['price-combo-edit'];

        // Validar datos obligatorios
        if (empty($comboid) || empty($combo) || empty($price)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }


        $update = $conexion->prepare('UPDATE combo SET combo = :combo, description = :description, price = :price WHERE id = :id');
        $update->bindParam(':combo', $combo);
        $update->bindParam(':description', $description);
        $update->bindParam(':price', $price);
        $update->bindParam(':id', $comboid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'El combo ha sido actualizado';
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}


//AGREGAR SECCION AL COMBO
if ($_POST['request'] == 'add-section') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-section-combo'];
        $type = $_POST['dinamic-section-combo'];

        if (empty($_POST['instruction-section-combo'])) {
            if ($type == 'default') {
                $instruction = 'Estos productos estan inlcuidos en el combo.';
            } else if ($type == 'unico') {
                $instruction = 'Seleccione uno de los siguientes productos.';
            } else if ($type == 'multiple') {
                $instruction = 'Selecione uno o mas productos de los siguientes.';
            }
        } else {
            $instruction = $_POST['instruction-section-combo'];
        }

        if (!isset($_POST['product-combo'])) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Seleccione al menos un producto.';
            echo json_encode($response);
            exit;
        }

        $products = $_POST['product-combo'] ?? [];
        $id_combo = $_POST['id-combo'];

        $insert = $conexion->prepare('INSERT INTO combo_groups(combo, `group`, `type`, instruction) VALUES (:combo, :group, :type, :instruction)');
        $insert->bindParam(':combo', $id_combo);
        $insert->bindParam(':group', $name);
        $insert->bindParam(':type', $type);
        $insert->bindParam(':instruction', $instruction);
        $insert->execute();
        $lastId = $conexion->lastInsertId();

        foreach ($products as $p) {
            list($type_product, $product, $variant, $extra) = explode('|', $p);
            $product = (int) $product;
            $variant = (int) $variant;

            if ($type_product == 'producto') {
                $item_one = $product;
                $item_two = null;
            } else if ($type_product == 'variante') {
                $item_one = $product;
                $item_two = $variant;
            } else if ($type_product == 'extra') {
                $item_one = $extra;
                $item_two = null;
            }

            insert_product($conexion, $lastId, $item_one, $item_two, $type_product);
        }

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Éxito';
        $response['message'] = 'Se agrego la seccion "' . $name . '"';
        $response['id'] = $id_combo;
        echo json_encode($response);

    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
        echo json_encode($response);
    }
}

if ($_POST['request'] == 'delete-section') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $id_section = $_POST['section'];
        $data = group($id_section);

        $delete = $conexion->prepare('DELETE FROM combo_groups WHERE id = :id');
        $delete->bindParam(':id', $id_section);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Sección Eliminada';
        $response['message'] = 'La sección ha sido eliminada de la lista';
        $response['id'] = $data['combo'];
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}

if ($_POST['request'] == 'qty-up') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $itemid = $_POST['item'];
        $info = item($itemid);
        $qty = $info['qty'] + 1;

        $update = $conexion->prepare('UPDATE combo_groups_products SET qty = :qty WHERE id = :id');
        $update->bindParam(':qty', $qty);
        $update->bindParam(':id', $itemid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'Cantidad del item aumentado';
        $response['qty'] = $qty;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}

if ($_POST['request'] == 'qty-down') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $itemid = $_POST['item'];
        $info = item($itemid);
        $qty = $info['qty'] - 1;

        if ($qty < 1) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Cantidad Invalida';
            echo json_encode($response);
            exit;
        }

        $update = $conexion->prepare('UPDATE combo_groups_products SET qty = :qty WHERE id = :id');
        $update->bindParam(':qty', $qty);
        $update->bindParam(':id', $itemid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'Cantidad del item aumentado';
        $response['qty'] = $qty;
    } catch (Exception $e) {
        // Si algo salió mal, revertimos la transacción
        $conexion->rollBack();

        $response['status'] = 500;
        $response['title'] = 'Error';
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
?>