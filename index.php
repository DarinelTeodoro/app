<?php
session_start();
include('cdn.html');
$app_name = 'Conejo Blanco';

if (!empty($_SESSION['data-useractive'])) {
    echo '<script>window.location.href = "src/users/'.$_SESSION['rol-useractive'].'/home.php";</script>';
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <title><?= $app_name ?></title>
  <link rel="manifest" href="manifest.webmanifest">
  <link href="style.css" rel="stylesheet">
  <link href="style-loader.css" rel="stylesheet">
  <link href="style-alert.css" rel="stylesheet">
  <link href="style-login.css" rel="stylesheet">
</head>

<body id="tag-body">

  <!--DISEÑO INDEX (CONTENEDOR PRINCIPAL)-->
  <div class="container-main-login">
    <!-- columna derecha -->
    <div class="column-presentation total-center">
      <div>
        <img src="files/fulllogo-nbg-light.png" class="img-logo-login">
      </div>
    </div>

    <!-- columna izquierda -->
    <div class="column-form">
      <div class="container-fluid">
        <div class="total-center">
          <img src="files/shortlogo-nbg.png" class="img-logo-login">
        </div>
        <span class="text-headline">Iniciar Sesión</span>
        <p>Ingresa tus credenciales para acceder a tu cuenta.</p>
      </div>

      <div class="container-fluid">
        <form method="post" action="" id="login-form">
          <div class="d-grid mb-2">
            <label for="key-user">Usuario</label>
            <input type="text" name="key-user" id="key-user">
          </div>
          <div class="d-grid mb-3">
            <label for="key-password">Contraseña</label>
            <input type="password" name="key-password" id="key-password">
          </div>
          <div class="d-grid mb-3">
            <button class="btn-execute object" id="btn-login">Ingresar</button>
          </div>
          <div class="align-between text-watermark">
            <span>@KaidaSystem</span>
            <span>V1.0.0</span>
          </div>
        </form>
      </div>
    </div>
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
      <img
        src="files/rabbit-face.webp" class="img-fluid">
    </div>

    <div class="loader">
      <svg viewBox="0 0 80 80">
        <rect height="64" width="64" y="8" x="8"></rect>
      </svg>
    </div>
  </div>

</body>

</html>

<script src="script.js"></script>
<script src="script-login.js"></script>