<?php
include('../model/db.php');
$product = $_POST['product'];
$data = product($product);

$list_categories = categories();
?>

<input type="hidden" name="id-product-edit" id="id-product-edit" value="<?= $product ?>" required>

<div class="total-center">
    <label for="img-product-edit" class="label-input-img" id="label-img-addproduct-edit" style="background: rgb(0, 0, 0, 0.3) url('../../../files/img_products/<?= $data['img'] ?>') center center / cover no-repeat;"><i
            class="fi fi-br-mode-landscape"></i></label>
    <input type="file" name="img-product-edit" id="img-product-edit">
</div>
<div class="d-grid mt-2">
    <label for="name-product-edit">Producto</label>
    <input type="text" name="name-product-edit" id="name-product-edit" value="<?= $data['product'] ?>"
        placeholder="Ej. Chilaquiles" required>
</div>
<div class="d-grid mt-2">
    <label for="description-product">Descripción</label>
    <textarea name="description-product-edit" id="description-product-edit" rows="3"
        placeholder="Ej. Platillo de chilaquiles"><?= $data['description'] ?></textarea>
</div>
<div class="d-grid mt-2">
    <label for="price-product-edit">Precio</label>
    <input type="number" step="0.01" min="0" name="price-product-edit" id="price-product-edit"
        value="<?= $data['price'] ?>" placeholder="0.00" required>
</div>
<div class="d-grid mt-2">
    <label for="category-product-edit">Categoria</label>
    <select name="category-product-edit" id="category-product-edit">
        <?php
        if ($list_categories) {
            foreach ($list_categories as $c) {
                ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $data['category'] ? 'selected' : '' ?>><?= $c['category'] ?>
                </option>
                <?php
            }
        }
        ?>
    </select>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>

<script>
    document.addEventListener('change', e => {
        if (e.target.id === 'img-product-edit') {
            img_product_edit(e, 'label-img-addproduct-edit');
        }
    });

    function img_product_edit(e, label) {
        const cont_img_product = document.getElementById(label);
        const input = e.target;
        const file = input.files?.[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                cont_img_product.style.background =
                    `rgb(0, 0, 0, 0.2) url(${ev.target.result}) center center / cover no-repeat`;
            };
            reader.readAsDataURL(file);
        } else {
            cont_img_product.style.background =
                `rgb(0, 0, 0, 0.2) url('../../../files/img_products/<?= $data['img'] ?>') center center / cover no-repeat`;
        }
    }
</script>