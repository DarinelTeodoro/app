<?php
include('../model/db.php');
$list_products = products();
$list_categories = categories();
function render_categories($categories)
{
    if (!$categories)
        return;

    foreach ($categories as $c) {
        /*if ($c['id'] == 1) {
            continue;
        }*/

        echo '<option value="' . $c['id'] . '">' .
            htmlspecialchars($c['category']) .
            '</option>';
    }
}
?>
<div class="modulo-header">
    <div><span class="text-headline">Productos</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addproduct">Agregar Producto</button>
    </div>
</div>

<div class="accordion" id="accordionItemsCategory">
    <?php
    $pxc = [];
    if ($list_products) {
        foreach ($list_products as $p) {
            $pid = $p['category'];
            $pxc[$pid][] = $p;
        }
    } else {
        echo '<div class="container-system-message">
            <i class="fi fi-br-restaurant"></i>
            <span>No hay productos registrados</span>
        </div>';
    }

    if ($list_categories) {
        foreach ($list_categories as $c) {
            $cid = $c['id'];
            $items = $pxc[$cid] ?? [];
            $np = count($items);

            if (!$np)
                continue;
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#panel<?= $c['id'] ?>" aria-controls="panel<?= $c['id'] ?>">
                        <span class="text-capitalize"><?= $c['category'] ?></span>
                    </button>
                </h2>
                <div id="panel<?= $c['id'] ?>" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="container-fluid">
                            <div class="row">
                                <?php foreach ($items as $i) { ?>
                                    <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1">
                                        <div class="card p-0">
                                            <div class="card-subcontainer">
                                                <div class="card-img"
                                                    style="background: rgb(0, 0, 0, 0.2) url('../../../files/img_products/<?= $i['img'] ?>') center center / cover no-repeat;">
                                                </div>
                                                <div class="p-2">
                                                    <div class="lh-2">
                                                        <div><b class="text-capitalize">
                                                                <?= $i['product'] ?>
                                                            </b></div>
                                                        <div>
                                                            <p class="text-muted lh-1">
                                                                <?= $i['description'] == null ? 'Sin Descripción' : $i['description'] ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <span class="text-success fw-bold">$
                                                                <?= number_format($i['price'], 2) ?>
                                                            </span>
                                                        </div>
                                                        <div class="container-options-card">
                                                            <button class="btn-edit" onclick="edit_product(<?= $i['id'] ?>)"><i
                                                                    class="fi fi-br-pencil"></i></button>
                                                            <button class="btn-delete" onclick="delete_product(<?= $i['id'] ?>)"><i
                                                                    class="fi fi-br-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>

<!-- Modal CREATE PRODUCT-->
<div class="modal fade" id="static-addproduct" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-addproductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addproductLabel">Agregar Producto</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addproduct-form">
                <div class="total-center">
                    <label for="img-product" class="label-input-img" id="label-img-addproduct"><i
                            class="fi fi-br-mode-landscape"></i></label>
                    <input type="file" name="img-product" id="img-product">
                </div>
                <div class="d-grid mt-2">
                    <label for="name-product">Producto</label>
                    <input type="text" name="name-product" id="name-product" placeholder="Ej. Chilaquiles" required>
                </div>
                <div class="d-grid mt-2">
                    <label for="description-product">Descripción</label>
                    <textarea name="description-product" id="description-product" rows="3"
                        placeholder="Ej. Platillo de chilaquiles"></textarea>
                </div>
                <div class="d-grid mt-2">
                    <label for="price-product">Precio</label>
                    <input type="number" step="0.01" min="0" name="price-product" id="price-product" placeholder="0.00"
                        required>
                </div>
                <div class="d-grid mt-2">
                    <label for="category-product">Categoria</label>
                    <select name="category-product" id="category-product">
                        <?= render_categories($list_categories) ?>
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

<!-- Modal EDIT PRODUCT-->
<div class="modal fade" id="static-editproduct" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-editproductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editproductLabel">Editar Producto</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editproduct-form">

            </form>
        </div>
    </div>
</div>


<script src="../../controller/manage-list-products-script.js"></script>