<?php
include('../../model/db.php');
$list_users = list_users();
?>
<div class="modulo-header">
    <div><span class="text-headline">Usuarios</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-adduser">Agregar Usuario</button></div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php
        if ($list_users) {
            foreach ($list_users as $u) {
                ?>
                <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1">
                    <div class="card">
                        <div class="lh-2">
                            <div><span class="text-capitalize"><?= $u['name'] ?></span></div>
                            <div><i class="fi fi-br-user-robot me-1"></i><span class="text-muted"><?= $u['username'] ?></span></div>
                        </div>
                        <div class="mt-1"><span class="rol-user"><?= $u['rol'] ?></span></div>
                        <div class="container-options-card">
                            <button class="btn-edit" onclick="edit_user(<?= $u['id'] ?>)"><i class="fi fi-br-pencil"></i></button>
                            <button class="btn-delete" onclick="delete_user(<?= $u['id'] ?>)"><i class="fi fi-br-trash"></i></button>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="container-system-message">
                <i class="fi fi-br-member-list"></i>
                <span>No hay usuarios registrados</span>
            </div>';
        }
        ?>
    </div>
</div>

<!-- Modal CREATE USER-->
<div class="modal fade" id="static-adduser" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-adduserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <div><span id="static-adduserLabel">Agregar Usuario</span></div>
        <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
      </div>
      <form class="modal-body" method="post" action="" id="adduser-form">
        <div class="d-grid">
            <label for="username-user">Usuario</label>
            <input type="text" name="username-user" id="username-user" placeholder="Ej. BetoPerez" required>
        </div>
        <div class="d-grid mt-2">
            <label for="name-user">Nombre</label>
            <input type="text" name="name-user" id="name-user" placeholder="Ej. Alberto Perez" required>
        </div>
        <div class="d-grid mt-2">
            <label for="psw-user">Contraseña</label>
            <input type="password" name="psw-user" id="psw-user" placeholder="********" required>
        </div>
        <div class="d-grid mt-2">
            <label for="rol-user">Rol</label>
            <select name="rol-user" id="rol-user">
                <option value="administrador">Administrador</option>
                <option value="cajero">Cajero</option>
                <option value="mesero">Mesero</option>
                <option value="cocina">Cocina</option>
                <option value="barra">Barra</option>
            </select>
        </div>

        <div class="mt-3 d-grid">
            <input type="hidden" name="request" value="create">
            <button type="submit" class="btn-execute object">Agregar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal EDIT USER-->
<div class="modal fade" id="static-edituser" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-edituserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <div><span id="static-edituserLabel">Editar Usuario</span></div>
        <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
      </div>
      <form class="modal-body" method="post" action="" id="edituser-form">
        
      </form>
    </div>
  </div>
</div>

<script src="management-user-script.js"></script>