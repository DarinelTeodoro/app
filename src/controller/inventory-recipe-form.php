<?php
include('../model/db.php');
$id = $_POST['item'];
$type = $_POST['type'];
?>

<input type="hidden" name="id-item" value="<?= $id ?>">
<input type="hidden" name="type-item" value="<?= $type ?>">

<div>
    <label class="d-block mb-1">Ingredientes / Materias Primas</label>
    <div id="items-list">
        <!-- Las filas se insertan aquí dinámicamente -->
    </div>
    <div class="text-end">
        <button type="button" class="btn-add" onclick="add_item_recipe()"><i class="fi fi-br-plus me-1"></i>
            Nuevo Elemento</button>
    </div>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="create">
    <button type="submit" class="btn-execute object">Agregar</button>
</div>