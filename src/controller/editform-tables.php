<?php
include('../model/db.php');
$data = settings('tables');
?>

<div class="d-grid mt-2">
    <label for="n-tables-edit">Cantidad de Mesas</label>
    <input type="number" step="1" min="0" name="n-tables-edit" id="n-tables-edit" value="<?= $data['value'] ?>" placeholder="0" required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit-tables">
    <button type="submit" class="btn-execute object">Editar</button>
</div>