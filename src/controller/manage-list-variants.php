<?php
include('../model/db.php');
$list_variants = variants();
$list_products = products();
function render_products($products)
{
    if (!$products)
        return;

    foreach ($products as $p) {
        echo '<option value="' . $p['id'] . '">' .
            htmlspecialchars($p['product']) .
            '</option>';
    }
}
?>
<div class="modulo-header">
    <div><span class="text-headline">Variantes</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addvariant">Agregar Variante</button>
    </div>
</div>

<div class="accordion" id="accordionVariantsItem">
    <?php
    $pxc = [];
    if ($list_variants) {
        foreach ($list_variants as $v) {
            $vid = $v['product'];
            $vxi[$vid][] = $v;
        }
    } else {
        echo '<div class="container-system-message">
            <i class="fi fi-br-restaurant"></i>
            <span>No hay variantes registradas</span>
        </div>';
    }
    ?>
    <div class="container-fluid">
        <div class="row">
            <?php
            if ($list_products) {
                foreach ($list_products as $p) {
                    $pid = $p['id'];
                    $subs = $vxi[$pid] ?? [];
                    $nv = count($subs);

                    if (!$nv)
                        continue;
                    ?>
                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panel<?= $p['id'] ?>" aria-controls="panel<?= $p['id'] ?>">
                                    <span class="text-capitalize"><?= $p['product'] ?></span> <span class="bg-danger p-1 pe-2 ps-2 ms-2 rounded"><?= $nv ?></span>
                                </button>
                            </h2>
                            <div id="panel<?= $p['id'] ?>" class="accordion-collapse collapse">
                                <div class="accordion-body p-1">

                                    <?php foreach ($subs as $s) { ?>
                                        <div class="card-variant">
                                            <div>
                                                <b class="text-capitalize">
                                                    <?= $s['variant'] ?>
                                                </b>
                                            </div>
                                            <div>
                                                <span class="text-success fw-bold">$
                                                    <?= number_format(($p['price'] + $s['increase']), 2) ?>
                                                </span>
                                            </div>
                                            <div class="container-options-card">
                                                <button class="btn-edit" onclick="edit_variant(<?= $s['id'] ?>)"><i
                                                        class="fi fi-br-pencil"></i></button>
                                                <button class="btn-delete" onclick="delete_variant(<?= $s['id'] ?>)"><i
                                                        class="fi fi-br-trash"></i></button>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- Modal CREATE VARIANT-->
<div class="modal fade" id="static-addvariant" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-addvariantLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addvariantLabel">Agregar Variante</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addvariant-form">
                <div class="d-grid mt-2">
                    <label for="name-variant">Variante</label>
                    <input type="text" name="name-variant" id="name-variant" placeholder="Ej. Vaso Chico" required>
                </div>
                <div class="d-grid mt-2">
                    <label for="product-variant">Producto</label>
                    <select name="product-variant" id="product-variant">
                        <?= render_products($list_products) ?>
                    </select>
                </div>
                <div class="d-grid mt-2">
                    <label for="plus-variant">Incremento</label>
                    <input type="number" step="0.01" min="0" name="plus-variant" id="plus-variant" placeholder="0.00"
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

<!-- Modal EDIT VARIANT-->
<div class="modal fade" id="static-editvariant" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-editvariantLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editvariantLabel">Editar Variante</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editvariant-form">

            </form>
        </div>
    </div>
</div>


<script src="../../controller/manage-list-variants-script.js"></script>