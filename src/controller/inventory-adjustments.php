<div class="modulo-header">
    <div><span class="text-headline">Inventario</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addadjustment">Agregar
            Movimiento</button>
    </div>
</div>

<div class="container-fluid">
    <table class="table" id="datatable-adjustments">
        <thead>
            <tr>
                <th class="align-middle text-start"></th>
                <th class="align-middle text-start">Fecha</th>
                <th class="align-middle text-center">Tipo de Ajuste</th>
                <th class="align-middle text-end">Detalles</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal CREATE MOVEMENT-->
<div class="modal fade" id="static-addadjustment" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-addadjustmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addadjustmentLabel">Agregar Movimiento</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addadjustment-form">
                <div class="d-grid">
                    <label for="type-adjustment">Movimiento</label>
                    <select name="type-adjustment" id="type-adjustment">
                        <option value="plus">Compra</option>
                        <option value="less">Descuento</option>
                    </select>
                </div>

                <div class="mt-3">
                    <label class="d-block mb-1">Ingredientes / Materias Primas</label>
                    <div id="materias-list">
                        <!-- Las filas se insertan aquí dinámicamente -->
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn-add" id="btn-add-materia"><i class="fi fi-br-plus me-1"></i>
                            Nuevo Elemento</button>
                    </div>
                </div>

                <div class="mt-3 d-grid">
                    <input type="hidden" name="request" value="create">
                    <button type="submit" class="btn-execute object">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal DETAILS MOVEMENT-->
<div class="modal fade" id="static-detailsmovement" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-detailsmovementLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-detailsmovementLabel">Detalles de Movimiento</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="detailsmovement-form">

            </form>
        </div>
    </div>
</div>

<script src="../../controller/inventory-adjustments-script.js"></script>