<?php
include('../model/db.php');
$category = $_POST['category'];

$data = category($category);
?>

<input type="hidden" name="id-category-edit" id="id-category-edit" value="<?= $category ?>" required>

<div class="d-grid">
    <label for="name-category-edit">Categoria</label>
    <input type="text" name="name-category-edit" id="name-category-edit" value="<?= $data['category'] ?>" placeholder="Ej. Bebidas en las rocas" required>
</div>
<div class="d-grid mt-2">
    <label for="description-category-edit">Descripción</label>
    <textarea name="description-category-edit" id="description-category-edit" rows="3"
        placeholder="Ej. Bebidas preparadas y con cubos de hielo"><?= $data['description'] ?></textarea>
</div>
<div class="d-grid mt-2">
    <label for="destination-category-edit">Destino</label>
    <select name="destination-category-edit" id="destination-category-edit">
        <option value="cocina" <?= $data['destination'] == 'cocina' ? 'selected' : '' ?>>Cocina</option>
        <option value="barra" <?= $data['destination'] == 'barra' ? 'selected' : '' ?>>Barra</option>
    </select>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>