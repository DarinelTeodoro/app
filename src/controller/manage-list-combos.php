<?php
include('../model/db.php');
$list_combos = combos();
?>
<div class="modulo-header">
    <div><span class="text-headline">Combos</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addcombo">Agregar Combo</button>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php
        if ($list_combos) {
            foreach ($list_combos as $c) {
                ?>
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1">
                    <div class="card">
                        <div class="lh-2">
                            <div><b class="text-capitalize"><?= $c['combo'] ?></b></div>
                            <div>
                                <p class="text-muted lh-1">
                                    <?= $c['description'] == null ? 'Sin Descripción' : $c['description'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-success fw-bold">$<?= number_format($c['price'], 2) ?></span>
                            </div>
                            <div class="container-options-card">
                                <button class="btn-config" onclick="config_combo(<?= $c['id'] ?>)"><i class="fi fi-br-hamburger-soda"></i></button>
                                <button class="btn-edit" onclick="edit_combo(<?= $c['id'] ?>)"><i
                                        class="fi fi-br-pencil"></i></button>
                                <button class="btn-delete" onclick="delete_combo(<?= $c['id'] ?>)"><i
                                        class="fi fi-br-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="container-system-message">
                <i class="fi fi-br-restaurant"></i>
                <span>No hay combos registrados</span>
            </div>';
        }
        ?>
    </div>
</div>

<!-- Modal CREATE COMBO-->
<div class="modal fade" id="static-addcombo" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-addcomboLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addcomboLabel">Agregar Combo</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addcombo-form">
                <div class="d-grid">
                    <label for="name-combo">Combo</label>
                    <input type="text" name="name-combo" id="name-combo" placeholder="Ej. BurgerMax" required>
                </div>
                <div class="d-grid mt-2">
                    <label for="description-combo">Descripción</label>
                    <textarea name="description-combo" id="description-combo" rows="3"
                        placeholder="Ej. Hamburguesa Hawaiana, 1 dip y un refresco de 600ml"></textarea>
                </div>
                <div class="d-grid mt-2">
                    <label for="price-combo">Precio</label>
                    <input type="number" step="0.01" min="0" name="price-combo" id="price-combo" placeholder="0.00"
                        required>
                </div>

                <div class="mt-3 d-grid">
                    <input type="hidden" name="request" value="create">
                    <button type="submit" class="btn-execute object">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT COMBO-->
<div class="modal fade" id="static-editcombo" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-editcomboLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editcomboLabel">Editar Combo</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editcombo-form">

            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT COMBO-->
<div class="modal fade" id="static-detailcombo" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-detailcomboLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-detailcomboLabel">Detalles del Combo</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <div class="modal-body" id="container-detailcombo">

            </div>
        </div>
    </div>
</div>

<!-- Modal ARM COMBO-->
<div class="modal fade" id="static-armcombo" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-armcomboLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <form class="modal-content" method="post" action="" id="armcombo-form">
            <div class="modal-header">
                <div><span id="static-armcomboLabel">Personalizar Combo</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <div class="modal-body" id="container-armcombo" style="overflow-x: hidden;">

            </div>
            <div class="modal-footer">
                <input type="hidden" name="request" value="add-section">
                <button type="submit" class="btn-execute">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="../../controller/manage-list-combos-script.js"></script>