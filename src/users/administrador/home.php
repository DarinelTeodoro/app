<?php
session_start();
$root = '../../../';
include($root.'cdn.html');
include('../../model/db.php');
$app_name = $_SESSION['app-name'];

if (empty($_SESSION['data-useractive'])) {
  header('Location: '.$root.'index.php');
} else {
  $id_user = $_SESSION['data-useractive'];
  $user = search_userid($id_user);

  if ($_SESSION['rol-useractive'] == 'mesero') {
        header('Location: ../mesero/home.php');
    } else if ($_SESSION['rol-useractive'] == 'caja') {
        header('Location: ../caja/home.php');
    } else if ($_SESSION['rol-useractive'] == 'barra') {
        header('Location: ../barra/home.php');
    } else if ($_SESSION['rol-useractive'] == 'cocina') {
        header('Location: ../cocina/home.php');
    }
}

if (isset($_POST['logout-session'])) {
  session_destroy();
  header('Location: '.$root.'index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="<?= $root ?>favicon.ico">
  <title><?= $app_name ?> - Administrador</title>
  <link href="<?= $root ?>style.css" rel="stylesheet">
  <link href="<?= $root ?>style-loader.css" rel="stylesheet">
  <link href="<?= $root ?>style-alert.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>

<body id="tag-body">

  <!--DISEÑO INDEX (CONTENEDOR PRINCIPAL)-->
  <div class="fixed-top system-navbar">
    <div class="d-flex align-items-center">
      <a href="home.php"><img src="<?= $root ?>files/rabbit-face.webp" class="navbar-logo"></a>
      <div class="lh-15">
        <div class="ms-1"><span class="text-headline">Bienvenido</span></div>
        <div class="ms-1"><span class="name-user"><?= $user['name'] ?></span></div>
      </div>
    </div>
    <div>
      <i class="bi bi-list" data-bs-toggle="offcanvas" data-bs-target="#staticMenu"
        aria-controls="staticMenu"></i>
    </div>
  </div>

  <!--MENU-->
  <div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticMenu"
    aria-labelledby="staticMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="staticMenuLabel">Conejo Blanco</h5>
      <i class="fi fi-br-cross icon-close" data-bs-dismiss="offcanvas" aria-label="Close"></i>
    </div>
    <form method="post" action="" class="offcanvas-body body-options-menu">
      <div><button type="button" class="btn-option-menu modulo" data-modulo="management-home" disabled><i class="fi fi-br-house-chimney-blank"></i><span>Inicio</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-menu"><i class="fi fi-br-hamburger-soda"></i><span>Menu</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-inventory"><i class="fi fi-br-supplier-alt"></i><span>Inventario</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-local"><i class="fi fi-br-lamp"></i><span>Local</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-order"><i class="fi fi-br-order-food-online"></i><span>Comandas</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-sale"><i class="fi fi-br-point-of-sale-bill"></i><span>Ventas</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-checkout"><i class="fi fi-br-cash-register"></i><span>Caja</span></button></div>
      <div class="mt-1"><button type="button" class="btn-option-menu modulo" data-modulo="management-user"><i class="fi fi-br-users-alt"></i><span>Usuarios</span></button></div>
      <div class="mt-1"><button type="submit" name="logout-session" class="btn-option-menu"><i class="fi fi-br-power"></i><span>Cerrar Sesión</span></button></div>
    </form>
  </div>

  <div class="container-main-home" id="container-main-home">
  </div>

  <!--ALERT-->
  <div class="fixed-top fullscreen total-center screen-alert" id="screen-alert">
    <div class="container-alert">
      <div class="container-title" id="title-alert">TITLE</div>
      <div class="container-message">
        <p class="text-center" id="message-alert">Message</p>
        <button class="btn-alert" onclick="hide_alert()">Aceptar</button>
      </div>
    </div>
  </div>

  <!--LOAD AFTER FORM-->
  <div class="fixed-top fullscreen total-center screen-load" id="screen-load">
    <div id="page">
      <div id="container-ring">
        <div id="ring"></div>
        <div id="ring"></div>
        <div id="ring"></div>
        <div id="ring"></div>
        <div id="h3">Cargando</div>
      </div>
    </div>
  </div>

  <!--LOAD MAIN-->
  <div class="fixed-top fullscreen total-center screen-preload" id="screen-preload">
    <div class="loader">
      <svg viewBox="0 0 80 80">
        <circle r="32" cy="40" cx="40" id="test"></circle>
      </svg>
    </div>

    <div class="loader triangle">
      <img src="<?= $root ?>files/rabbit-face.webp" class="img-fluid">
    </div>

    <div class="loader">
      <svg viewBox="0 0 80 80">
        <rect height="64" width="64" y="8" x="8"></rect>
      </svg>
    </div>
  </div>

</body>

</html>

<script src="<?= $root ?>script.js"></script>
<script src="script.js"></script>