<div class="modulo-header">
    <div><span class="text-headline">Recetas</span></div>
</div>

<div class="container-fluid">
    <table class="table" id="datatable-recipes">
        <thead>
            <tr>
                <th class="align-middle text-start"></th>
                <th class="align-middle text-start">Producto</th>
                <th class="align-middle text-center">Receta</th>
                <th class="align-middle text-center"></th>
                <th class="align-middle text-end"></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<!-- Modal CREATE RECIPE-->
<div class="modal fade" id="static-addrecipe" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-addrecipeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-addrecipeLabel">Configurar Receta</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="addrecipe-form">
                
            </form>
        </div>
    </div>
</div>

<!-- Modal DETAILS RECIPE-->
<div class="modal fade" id="static-detailsrecipe" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="static-detailsrecipeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-detailsrecipeLabel">Ingredientes / Materias Primas</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="detailsrecipe-form">

            </form>
        </div>
    </div>
</div>

<script src="../../controller/inventory-recipes-script.js"></script>