<?php
include('../model/db.php');
$combo = $_POST['combo'];

$data = combo($combo);
?>

<input type="hidden" name="id-combo-edit" id="id-combo-edit" value="<?= $combo ?>" required>

<div class="d-grid">
    <label for="name-combo-edit">Combo</label>
    <input type="text" name="name-combo-edit" id="name-combo-edit" value="<?= $data['combo'] ?>" placeholder="Ej. BurgerMax" required>
</div>
<div class="d-grid mt-2">
    <label for="description-combo-edit">Descripción</label>
    <textarea name="description-combo-edit" id="description-combo-edit" rows="3"
        placeholder="Ej. Hamburguesa Hawaiana, 1 dip y un refresco de 600ml"><?= $data['description'] ?></textarea>
</div>
<div class="d-grid mt-2">
    <label for="price-combo-edit">Precio</label>
    <input type="number" step="0.01" min="0" name="price-combo-edit" id="price-combo-edit" value="<?= $data['price'] ?>" placeholder="0.00" required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>