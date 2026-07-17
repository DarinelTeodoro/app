<?php
include('../model/db.php');
$carpeta = '../../files/img_products/';

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $name = $_POST['name-product'];
        $description = $_POST['description-product'];
        $price = $_POST['price-product'];
        $category = $_POST['category-product'];

        // Validar datos obligatorios
        if (empty($name) || empty($price)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        if ($_FILES['img-product']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['img-product'];
            $nombre_original = $archivo['name'];
            $tipo_temporal = $archivo['tmp_name'];
            $tamano = $archivo['size'];
            $error = $archivo['error'];

            // Validar tamaño del archivo (ejemplo: máximo 5MB)
            $tamano_maximo = 5 * 1024 * 1024; // 5MB
            if ($tamano > $tamano_maximo) {
                $response['status'] = 400;
                $response['alerta'] = 'Error';
                $response['message'] = 'El archivo es demasiado grande. Máximo 5MB.';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            // Extensión y validacion de formato
            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
            $formatos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($extension, $formatos_permitidos)) {
                $response['status'] = 400;
                $response['alerta'] = 'Error';
                $response['message'] = 'Formato de archivo no permitido. Use JPG, PNG, GIF o WEBP.';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            // Generar nombre único para evitar sobreescrituras
            $img_name = uniqid('img_', true) . '.' . $extension;
            $path = $carpeta . $img_name;

            // Crear directorio si no existe
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            // Mover el archivo temporal a su ubicación final
            if (!move_uploaded_file($tipo_temporal, $path)) {
                $response['status'] = 500;
                $response['alerta'] = 'Error';
                $response['message'] = 'Error al subir el archivo';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            $name_img = $img_name;
        } else {
            $name_img = 'default.webp';
        }

        $insert = $conexion->prepare('INSERT INTO product(product, description, img, price, category) VALUES (:product, :description, :img, :price, :category)');
        $insert->bindParam(':product', $name);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':img', $name_img);
        $insert->bindParam(':price', $price);
        $insert->bindParam(':category', $category);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Producto';
        $response['message'] = 'Se agrego el producto "' . $name . '"';
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
        $id_category = $_POST['product'];

        $delete = $conexion->prepare('DELETE FROM product WHERE id = :id');
        $delete->bindParam(':id', $id_category);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Producto Eliminado';
        $response['message'] = 'El producto ha sido eliminado de la lista';
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
        $productid = $_POST['id-product-edit'];
        $data = product($productid);

        $name = $_POST['name-product-edit'];
        $description = $_POST['description-product-edit'];
        $price = $_POST['price-product-edit'];
        $category = $_POST['category-product-edit'];

        // Validar datos obligatorios
        if (empty($name) || empty($price)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Llene los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        if ($_FILES['img-product-edit']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['img-product-edit'];
            $nombre_original = $archivo['name'];
            $tipo_temporal = $archivo['tmp_name'];
            $tamano = $archivo['size'];
            $error = $archivo['error'];

            // Validar tamaño del archivo (ejemplo: máximo 5MB)
            $tamano_maximo = 5 * 1024 * 1024; // 5MB
            if ($tamano > $tamano_maximo) {
                $response['status'] = 400;
                $response['alerta'] = 'Error';
                $response['message'] = 'El archivo es demasiado grande. Máximo 5MB.';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            // Extensión y validacion de formato
            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
            $formatos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($extension, $formatos_permitidos)) {
                $response['status'] = 400;
                $response['alerta'] = 'Error';
                $response['message'] = 'Formato de archivo no permitido. Use JPG, PNG, GIF o WEBP.';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            // Generar nombre único para evitar sobreescrituras
            $img_name = uniqid('img_', true) . '.' . $extension;
            $path = $carpeta . $img_name;

            // Crear directorio si no existe
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            // Mover el archivo temporal a su ubicación final
            if (!move_uploaded_file($tipo_temporal, $path)) {
                $response['status'] = 500;
                $response['alerta'] = 'Error';
                $response['message'] = 'Error al subir el archivo';
                $response['bg'] = 'danger';
                echo json_encode($response);
                exit;
            }

            $name_img = $img_name;
        } else {
            $name_img = $data['img'];
        }

        $update = $conexion->prepare('UPDATE product SET product = :product, description = :description, img = :img, price = :price, category = :category WHERE id = :id');
        $update->bindParam(':product', $name);
        $update->bindParam(':description', $description);
        $update->bindParam(':img', $name_img);
        $update->bindParam(':price', $price);
        $update->bindParam(':category', $category);
        $update->bindParam(':id', $productid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'El producto ha sido actualizado';
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