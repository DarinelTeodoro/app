<div class="modulo-header">
    <div><span class="text-headline">Ingredientes</span></div>
    <div><button class="btn-add" data-bs-toggle="modal" data-bs-target="#static-addingredient">Agregar
            Ingrediente</button>
    </div>
</div>

<div class="container-fluid">
    <table class="table" id="datatable-ingredients">
        <thead>
            <tr>
                <th class="align-middle text-start">Ingrediente</th>
                <th class="align-middle text-center">Inventario</th>
                <th class="align-middle text-end">Editar/Eliminar</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal CREATE INGREDIENT-->
<div class="modal fade" id="static-addingredient" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-addingredientLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addingredientLabel">Agregar Ingrediente</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addingredient-form">
                <div class="d-grid">
                    <label for="name-ingredient">Ingrediente</label>
                    <input type="text" name="name-ingredient" id="name-ingredient" placeholder="Ej. Pan de Hamburgesa"
                        required>
                </div>
                <div class="d-grid mt-2">
                    <label for="mode-ingredient">Presentacion/modalidad de compra</label>
                    <select name="mode-ingredient" id="mode-ingredient">
                        <option value="mayoreo">Caja / Reja / Paquete</option>
                        <option value="menudeo">Bolsa / Envase</option>
                        <option value="granel">A granel</option>
                    </select>
                </div>
                <div class="d-grid mt-2">
                    <label for="contpre-ingredient">Contenido presentacion</label>
                    <input type="number" name="contpre-ingredient" id="contpre-ingredient" step="1" min="0"
                        placeholder="0" required>
                </div>
                <div class="d-grid mt-2">
                    <label for="metric-ingredient">Medida del ingrediente</label>
                    <select name="metric-ingredient" id="metric-ingredient">
                        <option value="gramos">Gramos</option>
                        <option value="mililitros">Mililitros</option>
                        <option value="unidades">Unidades</option>
                    </select>
                </div>
                <div class="d-grid mt-2">
                    <label for="contunit-ingredient">Contenido unidad</label>
                    <input type="number" name="contunit-ingredient" id="contunit-ingredient" step="1" min="0"
                        placeholder="0" required>
                </div>

                <div class="mt-3 d-grid">
                    <input type="hidden" name="request" value="create">
                    <button type="submit" class="btn-execute object">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT INGREDIENT-->
<div class="modal fade" id="static-editingredient" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-editingredientLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-editingredientLabel">Editar Ingrediente</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="editingredient-form">

            </form>
        </div>
    </div>
</div>

<script src="../../controller/inventory-ingredients-script.js"></script>