var datatable_ingredients = $('#datatable-ingredients').DataTable({
    ajax: '../../controller/datatable-ingredients.php',
    columns: [
        { data: 'ingredient', className: 'align-middle text-start' },
        { data: 'reserva', className: 'align-middle text-center' },
        { data: 'actions', className: 'align-middle text-end' }
    ],
    ordering: false,
    order: [
        [0, 'asc']
    ]
});

function reload_datatableIngredients() {
    datatable_ingredients.ajax.reload(null, false);
}

$('#addingredient-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-ingredient.php',
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
                document.getElementById('addingredient-form').reset();
                bootstrap.Modal.getInstance(document.getElementById('static-addingredient')).hide();
                reload_datatableIngredients();
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

function delete_ingredient(id) {
    Swal.fire({
        title: '¿Deseas eliminar el ingrediente?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-ingredient.php',
                data: { ingredient: id, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        reload_datatableIngredients();
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

function edit_ingredient(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-editingredient')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-ingredient.php',
        data: { ingredient: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('editingredient-form');
        },
        success: function (response) {
            $("#editingredient-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('editingredient-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#editingredient-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-ingredient.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-editingredient')).hide();
                reload_datatableIngredients();
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