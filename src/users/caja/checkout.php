<?php
session_start();
$root = '../../../';
include($root . 'cdn.html');
include('../../model/db.php');
$app_name = $_SESSION['app-name'];

if (empty($_SESSION['data-useractive'])) {
    header('Location: ' . $root . 'index.php');
} else {
    $id_user = $_SESSION['data-useractive'];
    $user = search_userid($id_user);
}

if (isset($_POST['logout-session'])) {
    session_destroy();
    header('Location: ' . $root . 'index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= $root ?>favicon.ico">
    <title><?= $app_name ?> - Mesero</title>
    <link href="<?= $root ?>style.css" rel="stylesheet">
    <link href="<?= $root ?>style-loader.css" rel="stylesheet">
    <link href="<?= $root ?>style-alert.css" rel="stylesheet">
    <link href="style-checkout.css" rel="stylesheet">
</head>

<body id="tag-body">

    <!--DISEÑO INDEX (CONTENEDOR PRINCIPAL)-->
    <div class="fixed-top system-navbar">
        <div class="d-flex align-items-center">
            <div class="pe-2">
                <a type="button" href="home.php" class="btn-back">
                    <i class="fi fi-br-arrow-small-left"></i>
                </a>
            </div>
            <div class="d-flex align-items-center">
                <!--<a href="home.php"><img src="files/rabbit-mesero.png" class="navbar-logo"></a>-->
                <div class="lh-15">
                    <div class="ms-1"><span class="text-headline">Bienvenido</span></div>
                    <div class="ms-1"><span class="name-user"><?= $user['name'] ?></span></div>
                </div>
            </div>
        </div>
    </div>


    <div class="container-main-home" id="container-main-home">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <button type="button" class="btn-execute" onclick="get_init()">Monto
                Inicial</button>
            <button type="button" class="btn-execute" data-bs-toggle="modal"
                data-bs-target="#static-adjustmentBox">Ingresos / Gastos</button>
            <button type="button" class="btn-execute" data-bs-toggle="modal" data-bs-target="#static-cutBox">Corte de
                Caja</button>
        </div>
        <div class="container-fluid">
            <table class="table" id="datatable-movements">
                <thead>
                    <tr>
                        <th></th>
                        <th>Cantidad</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Concepto</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>



    <!-- Modal Monto Inicial-->
    <div class="modal fade" id="static-amountInit" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="static-amountInitLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="static-amountInitLabel">Cambiar Monto Inicial</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <form class="modal-body" method="post" action="" id="amountInit-form">
                    
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Corte de Caja-->
    <div class="modal fade" id="static-cutBox" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="static-cutBoxLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="static-cutBoxLabel">Corte de Caja</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <form class="modal-body" method="post" action="" id="cutBox-form">
                    <div class="d-grid gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$1,000</span>
                            <input type="number" name="mil" id="mil" class="form-control border border-primary" min="0"
                                placeholder="0">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$500</span>
                            <input type="number" name="quinientos" id="quinientos"
                                class="form-control border border-primary" min="0" placeholder="0">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$200</span>
                            <input type="number" name="doscientos" id="doscientos"
                                class="form-control border border-primary" min="0" placeholder="0">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$100</span>
                            <input type="number" name="cien" id="cien" class="form-control border border-primary"
                                min="0" placeholder="0">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$50</span>
                            <input type="number" name="cincuenta" id="cincuenta"
                                class="form-control border border-primary" min="0" placeholder="0">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border border-primary text-warning">$20</span>
                            <input type="number" name="veinte" id="veinte" class="form-control border border-primary"
                                min="0" placeholder="0">
                        </div>
                        <div class="input-group">
                            <span
                                class="input-group-text bg-dark border border-primary text-warning">$10/$5/$2/$1</span>
                            <input type="number" name="monedas" id="monedas" class="form-control border border-primary"
                                min="0" placeholder="0">
                        </div>
                    </div>

                    <div class="mt-3 d-grid">
                        <input type="hidden" name="request" value="update-cutBox">
                        <button type="submit" class="btn-execute object">Realizar Corte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Ajuste de caja-->
    <div class="modal fade" id="static-adjustmentBox" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="static-adjustmentBoxLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="static-adjustmentBoxLabel">Agregar Ajuste</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <form class="modal-body" method="post" action="" id="adjustmentBox-form">
                    <div class="d-grid">
                        <label for="type-adjustment">Tipo de ajuste</label>
                        <select name="type-adjustment" id="type-adjustment">
                            <option value="resta">Gasto</option>
                            <option value="suma">Ingreso</option>
                        </select>
                    </div>

                    <div class="d-grid mt-2">
                        <label for="amount-adjustment">Cantidad</label>
                        <input type="number" step="0.01" min="0" name="amount-adjustment" id="amount-adjustment"
                            placeholder="0.00" required>
                    </div>

                    <div class="d-grid mt-2">
                        <label for="motivo-adjustment">Motivo</label>
                        <textarea name="motivo-adjustment" id="motivo-adjustment"
                            placeholder="Ej. Compra de garrafones, pago de la luz, etc." required></textarea>
                    </div>

                    <div class="mt-3 d-grid">
                        <input type="hidden" name="request" value="update-adjustmentBox">
                        <button type="submit" class="btn-execute object">Actualizar</button>
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
<script src="script-checkout.js"></script>