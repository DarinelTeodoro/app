// var currentPage = 0;

var datatable_adjustments = $('#datatable-adjustments').DataTable({
    ajax: '../../controller/datatable-adjustments.php',
    columns: [
        { data: 'group', visible: false },
        { data: 'date', className: 'align-middle text-start' },
        { data: 'adjustment', className: 'align-middle text-center' },
        { data: 'actions', className: 'align-middle text-end' }
    ],
    ordering: false,
    order: [
        [0, 'desc']
    ],
    rowGroup: {
        dataSrc: 'group'
    }
});

/*
datatable_adjustments.on('page', function () {
    currentPage = datatable_adjustments.page();
});*/

function reload_datatableAdjustments() {
    /*datatable_adjustments.ajax.reload(function () {
        datatable_adjustments.page(currentPage).draw(false);
    });*/
    datatable_adjustments.ajax.reload(null, false);
}

if (typeof materiaOptions === 'undefined') var materiaOptions = [];

async function loadMaterias() {
    const res = await fetch('../../controller/inventory-get-materias.php');
    materiaOptions = await res.json();
}

// Llámalo al cargar la página
loadMaterias();

if (typeof rowCount === 'undefined') var rowCount = 0;

function buildMateriaOptions() {
    return materiaOptions.map(m =>
        `<option value="${m.id}">${m.nombre}</option>`
    ).join('');
}

function addMateriaRow() {
    rowCount++;
    const id = rowCount;
    const list = document.getElementById('materias-list');

    const row = document.createElement('div');
    row.className = 'container-fluid materia-row';
    row.dataset.rowId = id;

    row.innerHTML = `
        <div class="row mt-1 mb-1">
            <div class="col-7 p-0 pe-1">
                <select name="materias[${id}][id]" id="select-materia-${id}" required>
                    ${buildMateriaOptions()}
                </select>
            </div>
            <div class="col-3 p-0">
                <input type="number" name="materias[${id}][amount]" placeholder="0" step="1" min="0" required>
            </div>
            <div class="col-2 p-0 ps-1">
                <button type="button" class="btn-delete" onclick="removeMateriaRow(${id})" style="width: 100%; height: 100%;">
                    <i class="fi fi-br-trash"></i>
                </button>
            </div>
        </div>
    `;

    list.appendChild(row);
    /*
    <div class="row mb-1" id="detail-materia-${id}" style="font-size: 12px; color: var(--text-muted);">
            <!-- Se llena al cambiar el select -->
        </div> */

    // Cargar detalle al cambiar el select
    /*document.getElementById(`select-materia-${id}`).addEventListener('change', function () {
        loadMateriaDetail(this.value, id);
    });*/

    // Cargar detalle con el valor inicial
    /*const initialId = document.getElementById(`select-materia-${id}`).value;
    if (initialId) loadMateriaDetail(initialId, id);*/
}

/*function loadMateriaDetail(materia, rowId) {
    $.ajax({
        type: 'POST',
        url: '../../controller/inventory-get-materia-info.php',
        data: { materia: materia },
        dataType: 'json',
        success: function (detail) {
            if (!detail) return;
            document.getElementById(`detail-materia-${rowId}`).innerHTML = `
                <div class="col p-0">
                    <span>Presentación: <b>${detail.content_presentation}</b></span> &nbsp;
                    <span>Métrica: <b>${detail.metric}</b></span> &nbsp;
                    <span>Unidad: <b>${detail.content_unit}</b></span>
                </div>
            `;
        },
        error: function () {
            document.getElementById(`detail-materia-${rowId}`).innerHTML = '';
        }
    });
}*/

function removeMateriaRow(id) {
    const row = document.querySelector(`.materia-row[data-row-id="${id}"]`);
    if (row) row.remove();
}

document.getElementById('btn-add-materia')
    .addEventListener('click', addMateriaRow);

$('#addadjustment-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-adjustment.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            show_load();
        },
        success: function (response) {
            if (response.status === 201) {
                show_alert(response.title, response.message);
                document.getElementById('addadjustment-form').reset();
                document.getElementById('materias-list').innerHTML = ''; // limpia las filas
                rowCount = 0; // reinicia el contador
                bootstrap.Modal.getInstance(document.getElementById('static-addadjustment')).hide();
                reload_datatableAdjustments();
            } else {
                show_alert(response.title, response.message);
            }
        },
        //Manejo de error del servidor
        error: function (xhr, status, error) {
            show_alert('ERROR', 'Error al realizar operacion, Intente de nuevo');
        },
        complete: function () {
            hide_load();
        }
    });
});


function details_movement(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-detailsmovement')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/inventory-details-movement.php',
        data: { movement: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('detailsmovement-form');
        },
        success: function (response) {
            $("#detailsmovement-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('detailsmovement-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}
