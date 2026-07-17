<?php
include('../model/db.php');
$primary = $_POST['primary'];
$data = materia($primary);
?>

<input type="hidden" name="id-primary-edit" id="id-primary-edit" value="<?= $primary ?>" required>

<div class="d-grid">
    <label for="name-primary-edit">Ingrediente</label>
    <input type="text" name="name-primary-edit" id="name-primary-edit" value="<?= $data['materia'] ?>" placeholder="Ej. Pan de Hamburgesa" required>
</div>
<div class="d-grid mt-2">
    <label for="mode-primary-edit">Presentacion/modalidad de compra</label>
    <select name="mode-primary-edit" id="mode-primary-edit">
        <option value="mayoreo" <?= $data['presentation'] == 'mayoreo' ? 'selected' : '' ?>>Caja / Mayoreo</option>
        <option value="menudeo" <?= $data['presentation'] == 'menudeo' ? 'selected' : '' ?>>Paquete</option>
        <option value="granel" <?= $data['presentation'] == 'granel' ? 'selected' : '' ?>>Pieza</option>
    </select>
</div>
<div class="d-grid mt-2">
    <label for="content-primary-edit">Contenido presentacion</label>
    <input type="number" name="content-primary-edit" id="content-primary-edit" step="1" min="0" value="<?= $data['content_presentation'] ?>" placeholder="0"
        required>
</div>
<div class="d-grid mt-2">
    <label for="piece-primary-edit">Contenido unidad</label>
    <input type="number" name="piece-primary-edit" id="piece-primary-edit" step="1" min="0" value="<?= $data['content_unit'] ?>" placeholder="0"
        required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>