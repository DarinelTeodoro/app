<?php
session_start();
include('../model/db.php');

$response = array(); // Inicia array para response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['key-user']) && !empty($_POST['key-password'])) {
        $usuario = $_POST['key-user'];
        $Contrasena = $_POST["key-password"];

        $loguear = search_username($usuario);

        if ($loguear) { // Si existe el usuario
            $Contrasena_BD = $loguear['password']; // Trae la contraseña registrada

            if (password_verify($Contrasena, $Contrasena_BD)) { // Si las contraseñas son iguales
                $_SESSION['data-useractive'] = $loguear['id'];
                $_SESSION['rol-useractive'] = $loguear['rol'];
                $_SESSION['app-name'] = 'Conejo Blanco';

                $response['status'] = 'ACCGRANTED01';
                $response['message'] = 'Acceso autorizado';

                $rol = $loguear['rol'];

                switch ($rol) {
                    case 'administrador':
                        $response['path'] = 'src/users/administrador/home.php';
                        break;
                    case 'mesero':
                        $response['path'] = 'src/users/mesero/home.php';
                        break;
                    case 'cocina':
                        $response['path'] = 'src/users/cocina/home.php';
                        break;
                    case 'barra':
                        $response['path'] = 'src/users/barra/home.php';
                        break;
                    case 'cajero':
                        $response['path'] = 'src/users/caja/home.php';
                        break;
                    default:
                        session_destroy();
                        $response['path'] = 'index.php';
                }

            } else {
                $response['status'] = 'ERROR';
                $response['message'] = 'Correo/Contraseña incorrecta';
            }
        } else {
            $response['status'] = 'ERROR';
            $response['message'] = 'Correo/Contraseña incorrecta';
        }
    } else {
        $response['status'] = 'WARNING';
        $response['message'] = 'No deje campo(s) vacio(s)';
    }
} else {
    $response['status'] = 'ERROR';
    $response['message'] = 'Error de operación';
}

// Conversion de array a JSON para enviarlo al cliente
echo json_encode($response);
?>