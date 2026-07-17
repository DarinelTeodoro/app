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
    <link href="style-order.css" rel="stylesheet">
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
        <div>
            <button type="button" class="btn-order" data-bs-toggle="offcanvas" data-bs-target="#static-order">
                <i class="fi fi-br-shopping-cart"></i>
            </button>
        </div>
    </div>


    <div class="container-main-home container-menu-clients" id="container-main-home">
        <div id="menu-for-clients" class="cartas-grid"></div>
    </div>


    <!-- CARRITO -->
    <form method="post" action="" class="offcanvas offcanvas-end show" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="static-order">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Detalles de Orden</h5>
            <i class="fi fi-br-cross icon-close" data-bs-dismiss="offcanvas" aria-label="Close"></i>
        </div>
        <div class="offcanvas-body">
            <div class="d-grid">
                <label for="type-delivery">Tipo de entrega</label>
                <select name="type-delivery" id="type-delivery">
                    <option value="mesa">Mesa</option>
                    <option value="domicilio">Domicilio</option>
                </select>
            </div>
            <div class="mt-2" id="input-select-mesa">
                <label for="choose-table">Mesa</label>
                <select name="choose-table" id="choose-table">

                </select>
            </div>
            <div class="d-grid mt-2">
                <label for="name-client">Nombre del Cliente</label>
                <input type="text" name="name-client" id="name-client" placeholder="Ej. Alejandro Magno">
            </div>

            <div class="itemized-order" id="itemized-order">
                <div class="message-no-items">
                    <div class="mb-2">
                        <i class="fi fi-br-plate-utensils"></i>
                    </div>
                    <span>No hay productos agregados</span>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer d-flex align-items-center justify-content-between">
            <div class="d-flex flex-column lh-15">
                <small>Total</small>
                <b class="text-success"><span>$</span><span id="total-order">0.00</span></b>
            </div>
            <div>
                <button class="btn-add" type="button" id="btn-add-product">
                    <i class="fi fi-br-plus-small"></i>
                </button>
            </div>
            <div>
                <input type="hidden" name="request" value="add-order">
                <button type="submit" class="btn-execute object">Enviar</button>
            </div>
        </div>
    </form>


    <!--EXTRAS-->
    <div class="modal fade" id="static-extras" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="static-extrasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="static-extrasLabel">Personalizar producto</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" id="container-extras">
                    <span id="extras-product-name"></span>

                    <div class="d-grid mt-2">
                        <label>Extras</label>
                        <div id="extras-list"></div>
                        <div class="extras-resumen">
                            <strong>Total de extras:</strong>
                            <strong class="text-success" id="extras-total-monto">$0.00</strong>
                        </div>
                    </div>

                    <div class="d-grid mt-2">
                        <label for="extras-comment">Comentarios</label>
                        <textarea id="extras-comment" rows="3"
                            placeholder="Ej. sin cebolla, término medio..."></textarea>
                    </div>

                    <!--<div class="d-grid mt-2">
                        <label>Cantidad</label>
                        <div class="qty-control">
                            <button type="button" id="extras-qty-menos">-</button>
                            <span id="extras-qty-valor">1</span>
                            <button type="button" id="extras-qty-mas">+</button>
                        </div>
                    </div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-confirm-extras" id="btn-confirm-extras">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!--VARIANTES-->
    <div class="modal fade" id="static-variantes" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="static-variantesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="variantes-product-name"></span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" id="variantes-list"></div>
            </div>
        </div>
    </div>


    <!-- COMBOS -->
    <div class="modal fade" id="static-combo" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="combo-product-name"></span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" style="overflow-x: hidden;">
                    <div id="combo-groups-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-confirm-combo">Confirmar</button>
                </div>
            </div>
        </div>
    </div>


    <!--Nuevo producto-->
    <div class="modal fade" id="static-new-product" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="combo-product-name">Producto Especial</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <label for="new-product-name">Nombre del producto</label>
                        <input type="text" id="new-product-name" placeholder="Ej. Torta especial" required>
                    </div>
                    <div class="d-grid mt-2">
                        <label for="new-product-destination">Destino</label>
                        <select id="new-product-destination">
                            <option value="cocina">Cocina</option>
                            <option value="barra">Barra</option>
                        </select>
                    </div>
                    <div class="d-grid mt-2">
                        <label for="new-product-price">Costo</label>
                        <input type="number" id="new-product-price" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-confirm-new-product">Continuar</button>
                </div>
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

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/eruda/3.0.1/eruda.min.js"></script>
    <script>eruda.init();</script>-->
</body>

</html>

<script src="<?= $root ?>script.js"></script>
<script src="script-new-comanda.js"></script>