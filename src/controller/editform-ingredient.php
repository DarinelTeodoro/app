<?php
include('../model/db.php');
$ingredient = $_POST['ingredient'];
$data = materia($ingredient);
?>

<input type="hidden" name="id-ingredient-edit" id="id-ingredient-edit" value="<?= $ingredient ?>" required>

<div class="d-grid">
    <label for="name-ingredient-edit">Ingrediente</label>
    <input type="text" name="name-ingredient-edit" id="name-ingredient-edit" value="<?= $data['materia'] ?>" placeholder="Ej. Pan de Hamburgesa" required>
</div>
<div class="d-grid mt-2">
    <label for="mode-ingredient-edit">Presentacion/modalidad de compra</label>
    <select name="mode-ingredient-edit" id="mode-ingredient-edit">
        <option value="mayoreo" <?= $data['presentation'] == 'mayoreo' ? 'selected' : '' ?>>Caja / Reja / Paquete</option>
        <option value="menudeo" <?= $data['presentation'] == 'menudeo' ? 'selected' : '' ?>>Bolsa / Envase</option>
        <option value="granel" <?= $data['presentation'] == 'granel' ? 'selected' : '' ?>>A granel</option>
    </select>
</div>
<div class="d-grid mt-2">
    <label for="contpre-ingredient-edit">Contenido presentacion</label>
    <input type="number" name="contpre-ingredient-edit" id="contpre-ingredient-edit" step="1" min="0" value="<?= $data['content_presentation'] ?>" placeholder="0"
        required>
</div>
<div class="d-grid mt-2">
    <label for="metric-ingredient-edit">Medida del ingrediente</label>
    <select name="metric-ingredient-edit" id="metric-ingredient-edit">
        <option value="gramos" <?= $data['metric'] == 'gramos' ? 'selected' : '' ?>>Gramos</option>
        <option value="mililitros" <?= $data['metric'] == 'mililitros' ? 'selected' : '' ?>>Mililitros</option>
        <option value="unidades" <?= $data['metric'] == 'unidades' ? 'selected' : '' ?>>Unidades</option>
    </select>
</div>
<div class="d-grid mt-2">
    <label for="contunit-ingredient-edit">Contenido unidad</label>
    <input type="number" name="contunit-ingredient-edit" id="contunit-ingredient-edit" step="1" min="0" value="<?= $data['content_unit'] ?>" placeholder="0"
        required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>