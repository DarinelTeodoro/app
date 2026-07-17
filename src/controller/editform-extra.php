<?php
include('../model/db.php');
$extra = $_POST['extra'];
$data = extra($extra);
?>

<input type="hidden" name="id-extra-edit" id="id-extra-edit" value="<?= $extra ?>" required>

<div class="d-grid mt-2">
    <label for="name-extra-edit">Variante</label>
    <input type="text" name="name-extra-edit" id="name-extra-edit" value="<?= $data['extra'] ?>" placeholder="Ej. Vaso Chico" required>
</div>
<div class="d-grid mt-2">
    <label for="destination-extra-edit">Destino</label>
    <select name="destination-extra-edit" id="destination-extra-edit">
        <option value="cocina" <?= $data['destination'] == 'cocina' ? 'selected' : '' ?>>Cocina</option>
        <option value="barra" <?= $data['destination'] == 'barra' ? 'selected' : '' ?>>Barra</option>
    </select>
</div>
<div class="d-grid mt-2">
    <label for="plus-variant">Incremento</label>
    <input type="number" step="0.01" min="0" name="price-extra-edit" id="price-extra-edit" value="<?= $data['price'] ?>" placeholder="0.00" required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>