<?php
include('../model/db.php');

if ($_POST['request'] == 'create') {
    // Iniciar transacción
    $conexion = new Conexion();
    $conexion->beginTransaction();

    try {
        $username = $_POST['username-user'];
        $name = $_POST['name-user'];
        $password = $_POST['psw-user'];
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $rol = $_POST['rol-user'];

        // Validar datos obligatorios
        if (empty($username) || empty($name) || empty($password)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'Todos los campos son obligatorios';
            echo json_encode($response);
            exit;
        }

        $exist = search_username($username);

        //Verificar que el usuario no exista
        if ($exist) {
            $response['status'] = 400;
            $response['title'] = 'Usuario no Disponible';
            $response['message'] = 'El usuario ya esta vinculado a una cuenta activa';
            echo json_encode($response);
            exit;
        }

        $insert = $conexion->prepare('INSERT INTO user(username, name, password, rol) VALUES (:user, :name, :psw, :rol)');
        $insert->bindParam(':user', $username);
        $insert->bindParam(':name', $name);
        $insert->bindParam(':psw', $passwordHashed);
        $insert->bindParam(':rol', $rol);
        $insert->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Nuevo Usuario';
        $response['message'] = 'Usuario agregado exitosamente';
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
        $id_user = $_POST['user'];

        $delete = $conexion->prepare('UPDATE user SET status = 0 WHERE id = :id');
        $delete->bindParam(':id', $id_user);
        $delete->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Usuario Eliminado';
        $response['message'] = 'Usuario dado de baja';
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
        $userid = $_POST['id-user-edit'];
        $name = $_POST['name-user-edit'];
        $rol = $_POST['rol-user-edit'];

        // Validar datos obligatorios
        if (empty($userid) || empty($name)) {
            $response['status'] = 400;
            $response['title'] = 'Alerta';
            $response['message'] = 'No deje vacios los campos obligatorios';
            echo json_encode($response);
            exit;
        }

        $exist = search_userid($userid);

        //Verificar que el usuario no exista
        if (!$exist) {
            $response['status'] = 400;
            $response['title'] = 'Usuario Invalido';
            $response['message'] = 'El usuario no existe';
            echo json_encode($response);
            exit;
        }

        if (!empty($_POST['psw-user-edit'])) {
            $password = $_POST['psw-user-edit'];
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

            $update = $conexion->prepare('UPDATE user SET name = :name, password = :psw, rol = :rol WHERE id = :user');
            $update->bindParam(':psw', $passwordHashed);
        } else {
            $update = $conexion->prepare('UPDATE user SET name = :name, rol = :rol WHERE id = :user');
        }
        
        $update->bindParam(':name', $name);
        $update->bindParam(':rol', $rol);
        $update->bindParam(':user', $userid);
        $update->execute();

        // Si todo salió bien, confirmamos la transacción
        $conexion->commit();

        $response['status'] = 201;
        $response['title'] = 'Datos Actualizados';
        $response['message'] = 'Los datos del usuario han sido actualizados';
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