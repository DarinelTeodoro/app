<?php
include('../../model/db.php');
$data = get_init();
?>

<div class="d-grid">
    <label for="amount-init">Cantidad Inicial</label>
    <input type="number" step="0.01" min="0" name="amount-init" id="amount-init" value="<?= $data['inicial'] ?>" required>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="update-amountInit">
    <button type="submit" class="btn-execute object">Actualizar</button>
</div>