<div>
    <div class="modulo-header">
        <div><span class="text-headline">Listado de Comandas</span></div>
        <div><button class="btn-execute" onclick="reload_datatableOrders()">Actualizar Tabla</button></div>
    </div>

    <div class="container-fluid">
        <table class="table" id="datatable-orders">
            <thead>
                <tr>
                    <th></th>
                    <th>Comanda</th>
                    <th>Estado</th>
                    <th>Hora</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal DETALLES ORDEN-->
<div class="modal fade" id="static-managementorder" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-managementorderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-managementorderLabel">Detalles de Orden</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <div class="modal-body" id="managementorder-form">

            </div>
        </div>
    </div>
</div>

<script src="management-order-script.js"></script>