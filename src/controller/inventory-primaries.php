<div class="modulo-header">
    <div><span class="text-headline">Materias Primas</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addprimary">Agregar
            Materia</button>
    </div>
</div>

<div class="container-fluid">
    <table class="table" id="datatable-primaries">
        <thead>
            <tr>
                <th class="align-middle text-start">Fecha</th>
                <th class="align-middle text-center">Tipo de Ajuste</th>
                <th class="align-middle text-end">Detalles</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal CREATE MATERIA-->
<div class="modal fade" id="static-addprimary" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-addprimaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addprimaryLabel">Agregar Materia</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addprimary-form">
                <div class="d-grid">
                    <label for="name-primary">Materia Prima</label>
                    <input type="text" name="name-primary" id="name-primary" placeholder="Ej. Vasos desechables"
                        required>
                </div>
                <div class="d-grid mt-2">
                    <label for="mode-primary">Presentacion/modalidad de compra</label>
                    <select name="mode-primary" id="mode-primary">
                        <option value="mayoreo">Caja / Mayoreo</option>
                        <option value="menudeo">Paquete</option>
                        <option value="granel">Pieza</option>
                    </select>
                </div>
                <div class="d-grid mt-2">
                    <label for="content-primary">Contenido presentacion</label>
                    <input type="number" name="content-primary" id="content-primary" step="1" min="0" placeholder="0"
                        required>
                </div>
                <div class="d-grid mt-2">
                    <label for="piece-primary">Piezas</label>
                    <input type="number" name="piece-primary" id="piece-primary" step="1" min="0" placeholder="0"
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

<!-- Modal EDIT MATERIA-->
<div class="modal fade" id="static-editprimary" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-editprimaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editprimaryLabel">Editar Materia</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editprimary-form">

            </form>
        </div>
    </div>
</div>

<script src="../../controller/inventory-primaries-script.js"></script>