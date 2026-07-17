var itemsPage = 0;

var datatable_recipes = $('#datatable-recipes').DataTable({
    ajax: '../../controller/datatable-recipes.php',
    columns: [
        { data: 'group', visible: false, searchable: true },
        { data: 'product', className: 'align-middle text-start', searchable: true },
        { data: 'recipe', className: 'align-middle text-center', searchable: false },
        { data: 'recipe_text', visible: false, searchable: true },
        { data: 'actions', className: 'align-middle text-end', searchable: false }
    ],
    ordering: false,
    order: [
        [0, 'desc'],
        [1, 'desc']
    ],
    rowGroup: {
        dataSrc: 'group'
    }
});


datatable_recipes.on('page', function () {
    itemsPage = datatable_recipes.page();
});

function reload_datatableRecipes() {
    datatable_recipes.ajax.reload(function () {
        datatable_recipes.page(itemsPage).draw(false);
    });
}

if (typeof itemOptions === 'undefined') var itemOptions = [];

async function loadItems() {
    const res = await fetch('../../controller/inventory-get-materias.php');
    itemOptions = await res.json();
}

// Llámalo al cargar la página
loadItems();

if (typeof rowCount === 'undefined') var rowCount = 0;

function buildItemOptions() {
    return itemOptions.map(m =>
        `<option value="${m.id}">${m.nombre}</option>`
    ).join('');
}

function add_item_recipe() {
    rowCount++;
    const id = rowCount;
    const list = document.getElementById('items-list');

    const row = document.createElement('div');
    row.className = 'container-fluid item-row';
    row.dataset.rowId = id;

    row.innerHTML = `
        <div class="row mt-1 mb-1">
            <div class="col-7 p-0 pe-1">
                <select name="items[${id}][materia]" id="select-item-${id}">
                    ${buildItemOptions()}
                </select>
            </div>
            <div class="col-3 d-flex  align-items-center p-0">
                <input type="number" name="items[${id}][value]" placeholder="0" step="1" min="0" required><div class="ms-1" id="detail-item-${id}"></div>
            </div>
            <div class="col-2 p-0 ps-1">
                <button type="button" class="btn-delete" onclick="remove_item_recipe(${id})" style="width: 100%; height: 100%;">
                    <i class="fi fi-br-trash"></i>
                </button>
            </div>
        </div>
    `;

    list.appendChild(row);

    // Cargar detalle al cambiar el select
    document.getElementById(`select-item-${id}`).addEventListener('change', function () {
        loadItemDetail(this.value, id);
    });

    // Cargar detalle con el valor inicial
    const initialId = document.getElementById(`select-item-${id}`).value;
    if (initialId) loadItemDetail(initialId, id);
}

function loadItemDetail(materia, rowId) {
    $.ajax({
        type: 'POST',
        url: '../../controller/inventory-get-materia-info.php',
        data: { materia: materia },
        dataType: 'json',
        success: function (detail) {
            if (!detail) return;
            document.getElementById(`detail-item-${rowId}`).innerHTML = `<span>${detail.metric}</span>`;
        },
        error: function () {
            document.getElementById(`detail-item-${rowId}`).innerHTML = '';
        }
    });
}

function remove_item_recipe(id) {
    const row = document.querySelector(`.item-row[data-row-id="${id}"]`);
    if (row) row.remove();
}


function add_recipe(id, type) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-addrecipe')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/inventory-recipe-form.php',
        data: { item: id, type: type },
        dataType: 'html',
        beforeSend: function () {
            animation_load('addrecipe-form');
        },
        success: function (response) {
            $("#addrecipe-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('addrecipe-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#addrecipe-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-recipe.php',
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
                document.getElementById('addrecipe-form').reset();
                document.getElementById('items-list').innerHTML = ''; // limpia las filas
                rowCount = 0; // reinicia el contador
                bootstrap.Modal.getInstance(document.getElementById('static-addrecipe')).hide();
                reload_datatableRecipes();
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



function details_recipe(id, type) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-detailsrecipe')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/inventory-recipe-detail.php',
        data: { item: id, type: type },
        dataType: 'html',
        beforeSend: function () {
            animation_load('detailsrecipe-form');
        },
        success: function (response) {
            $("#detailsrecipe-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('detailsrecipe-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}


function delete_recipe(product, type, materia) {
    Swal.fire({
        title: '¿Deseas eliminar ingrediente / materia?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-recipe.php',
                data: { materia: materia, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        details_recipe(product, type);
                        reload_datatableRecipes();
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
        }
    });
}
