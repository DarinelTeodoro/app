$('#addcategory-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-category.php',
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
                document.getElementById('addcategory-form').reset();
                bootstrap.Modal.getInstance(document.getElementById('static-addcategory')).hide();
                load_section('../../controller/manage-list-categories.php', 'container-secondary');
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

function delete_category(id) {
    Swal.fire({
        title: '¿Deseas eliminar la categoria?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-category.php',
                data: { category: id, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        load_section('../../controller/manage-list-categories.php', 'container-secondary');
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

function edit_category(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-editcategory')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-category.php',
        data: { category: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('editcategory-form');
        },
        success: function (response) {
            $("#editcategory-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('editcategory-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#editcategory-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-category.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-editcategory')).hide();
                load_section('../../controller/manage-list-categories.php', 'container-secondary');
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