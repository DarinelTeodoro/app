<?php
include('../model/db.php');
$list_categories = categories();
?>
<div class="modulo-header">
    <div><span class="text-headline">Categorias</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addcategory">Agregar Categoria</button>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php
        $n = count($list_categories);

        if ($n > 1) {
            foreach ($list_categories as $c) {
                if ($c['id'] == 1) {
                    continue;
                }
                ?>
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1">
                    <div class="card">
                        <div class="lh-2">
                            <div><b class="text-capitalize"><?= $c['category'] ?></b></div>
                            <div>
                                <p class="text-muted lh-1">
                                    <?= $c['description'] == null ? 'Sin Descripción' : $c['description'] ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <?=
                                $c['destination'] == 'cocina' ? '<span class="p-1 ps-2 pe-2 bg-warning rounded info-destination"><i class="fi fi-br-pan-frying"></i> Cocina</span>' : '<span class="p-1 ps-2 pe-2 bg-primary text-white rounded info-destination"><i class="fi fi-br-mug-hot"></i> Barra</span>';
                                ?>
                            </div>
                            <div class="container-options-card">
                                <button class="btn-edit" onclick="edit_category(<?= $c['id'] ?>)"><i
                                        class="fi fi-br-pencil"></i></button>
                                <button class="btn-delete" onclick="delete_category(<?= $c['id'] ?>)"><i
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
                <span>No hay categorias registradas</span>
            </div>';
        }
        ?>
    </div>
</div>

<!-- Modal CREATE CATEGORY-->
<div class="modal fade" id="static-addcategory" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-addcategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addcategoryLabel">Agregar Categoria</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addcategory-form">
                <div class="d-grid">
                    <label for="name-category">Categoria</label>
                    <input type="text" name="name-category" id="name-category" placeholder="Ej. Bebidas en las rocas"
                        required>
                </div>
                <div class="d-grid mt-2">
                    <label for="description-category">Descripción</label>
                    <textarea name="description-category" id="description-category" rows="3"
                        placeholder="Ej. Bebidas preparadas y con cubos de hielo"></textarea>
                </div>
                <div class="d-grid mt-2">
                    <label for="destination-category">Destino</label>
                    <select name="destination-category" id="destination-category">
                        <option value="cocina">Cocina</option>
                        <option value="barra">Barra</option>
                    </select>
                </div>

                <div class="mt-3 d-grid">
                    <input type="hidden" name="request" value="create">
                    <button type="submit" class="btn-execute object">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT CATEGORY-->
<div class="modal fade" id="static-editcategory" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-editcategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editcategoryLabel">Editar Categoria</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editcategory-form">

            </form>
        </div>
    </div>
</div>

<script src="../../controller/manage-list-categories-script.js"></script>