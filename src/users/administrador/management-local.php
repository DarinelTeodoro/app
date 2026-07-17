<?php
include('../../model/db.php');
?>

<style>
    .submenu-menu .option-submenu {
        min-width: 132px;
    }
</style>

<div id="container-secondary">
    <div class="modulo-header">
        <div><span class="text-headline">Local</span></div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-4 col-md-6 col-sm-12">
                <div class="tarjet-double-column">
                    <div>
                        <div class="text-primary fw-bold">Mesas</div>
                        <div><span class="fs-3" id="n-tables-local">0</span></div>
                        <div class="fz-tag text-muted">Cantidad de mesas que hay en el local</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn-edit" onclick="setting_tables()">Editar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal EDIT MESAS-->
<div class="modal fade" id="static-edittables" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="static-edittablesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div><span id="static-edittablesLabel">Editar Mesas</span></div>
                <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
            </div>
            <form class="modal-body" method="post" action="" id="edittables-form">

            </form>
        </div>
    </div>
</div>

<script src="management-local-script.js"></script>