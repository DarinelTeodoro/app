<?php
include('../model/db.php');
$variant = $_POST['variant'];
$list_products = products();
$data = variant($variant);
?>

<input type="hidden" name="id-variant-edit" id="id-variant-edit" value="<?= $variant ?>" required>

<div class="d-grid mt-2">
    <label for="name-variant-edit">Variante</label>
    <input type="text" name="name-variant-edit" id="name-variant-edit" value="<?= $data['variant'] ?>" placeholder="Ej. Vaso Chico" required>
</div>
<div class="d-grid mt-2">
    <label for="product-variant-edit">Producto</label>
    <select name="product-variant-edit" id="product-variant-edit">
        <?php
        if ($list_products) {
            foreach ($list_products as $p) {
                ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $data['product'] ? 'selected' : '' ?>><?= $p['product'] ?></option>
                <?php
            }
        }
        ?>
    </select>
</div>
<div class="d-grid mt-2">
    <label for="plus-variant">Incremento</label>
    <input type="number" step="0.01" min="0" name="plus-variant-edit" id="plus-variant-edit" value="<?= $data['increase'] ?>" placeholder="0.00" required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>