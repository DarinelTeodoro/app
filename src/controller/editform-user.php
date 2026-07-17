<?php
include('../model/db.php');
$user = $_POST['user'];

$data = search_userid($user);
?>

<input type="hidden" name="id-user-edit" id="id-user-edit" value="<?= $user ?>" required>

<div class="d-grid">
    <label for="username-user-edit">Usuario</label>
    <input type="text" name="username-user-edit" id="username-user-edit" value="<?= $data['username'] ?>" readonly>
</div>
<div class="d-grid mt-2">
    <label for="name-user-edit">Nombre</label>
    <input type="text" name="name-user-edit" id="name-user-edit" value="<?= $data['name'] ?>" placeholder="Ej. Alberto Perez" required>
</div>
<div class="d-grid mt-2">
    <label for="psw-user-edit">Contraseña</label>
    <input type="password" name="psw-user-edit" id="psw-user-edit" placeholder="********">
    <span class="text-indication">**LLenar este campo solo si se desea cambiar la contraseña</span>
</div>
<div class="d-grid mt-2">
    <label for="rol-user-edit">Rol</label>
    <select name="rol-user-edit" id="rol-user-edit">
        <option value="administrador" <?= $data['rol'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
        <option value="cajero" <?= $data['rol'] == 'cajero' ? 'selected' : '' ?>>Cajero</option>
        <option value="mesero" <?= $data['rol'] == 'mesero' ? 'selected' : '' ?>>Mesero</option>
        <option value="cocina" <?= $data['rol'] == 'cocina' ? 'selected' : '' ?>>Cocina</option>
        <option value="barra" <?= $data['rol'] == 'barra' ? 'selected' : '' ?>>Barra</option>
    </select>
</div>

<div class="mt-3 d-grid">
    <input type="hidden" name="request" value="edit">
    <button type="submit" class="btn-execute object">Editar</button>
</div>