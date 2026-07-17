<?php
include('../model/db.php');
$list_extras = extras();
?>
<div class="modulo-header">
    <div><span class="text-headline">Extras</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addextra">Agregar Extra</button>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th class="align-middle"></th>
                <th class="align-middle text-start">Extra</th>
                <th class="align-middle text-center">Precio</th>
                <th class="align-middle"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($list_extras) {
                foreach ($list_extras as $e) {
                    ?>
                    <tr>
                        <td class="align-middle text-center"><?= $e['destination'] == 'cocina' ? '<span class="p-1 ps-2 pe-2 bg-warning rounded info-destination"><i class="fi fi-br-pan-frying"></i></span>' : '<span class="p-1 ps-2 pe-2 bg-primary text-white rounded info-destination"><i class="fi fi-br-mug-hot"></i></span>'; ?></td>
                        <td class="align-middle text-start"><?= $e['extra'] ?></td>
                        <td class="align-middle text-center"><span class="text-success fw-bold">$<?= number_format($e['price'], 2) ?></span></td>
                        <td class="align-middle text-end">
                            <button class="btn-edit" onclick="edit_extra(<?= $e['id'] ?>)"><i
                                    class="fi fi-br-pencil"></i></button>
                            <button class="btn-delete" onclick="delete_extra(<?= $e['id'] ?>)"><i
                                    class="fi fi-br-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="text-center">
                    <td colspan="4">No hay extras registrados</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>


<!-- Modal CREATE EXTRA-->
<div class="modal fade" id="static-addextra" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-addextraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addextraLabel">Agregar Extra</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addextra-form">
                <div class="d-grid mt-2">
                    <label for="name-extra">Variante</label>
                    <input type="text" name="name-extra" id="name-extra" placeholder="Ej. Dip Mostaza" required>
                </div>
                <div class="d-grid mt-2">
                    <label for="destination-extra">Destino</label>
                    <select name="destination-extra" id="destination-extra">
                        <option value="cocina">Cocina</option>
                        <option value="barra">Barra</option>
                    </select>
                </div>
                <div class="d-grid mt-2">
                    <label for="price-extra">Precio</label>
                    <input type="number" step="0.01" min="0" name="price-extra" id="price-extra" placeholder="0.00"
                        required>
                </div>

                <div class="mt-3 d-grid">
                    <input type="hidden" name="request" value="create">
                    <button type="submit" class="btn-execute object">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT EXTRA-->
<div class="modal fade" id="static-editextra" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-editextraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editextraLabel">Editar Extra</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editextra-form">

            </form>
        </div>
    </div>
</div>


<script src="../../controller/manage-list-extras-script.js"></script>