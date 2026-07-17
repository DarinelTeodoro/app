var datatable_primaries = $('#datatable-primaries').DataTable({
    ajax: '../../controller/datatable-primaries.php',
    columns: [
        { data: 'materia', className: 'align-middle text-start' },
        { data: 'reserva', className: 'align-middle text-center' },
        { data: 'actions', className: 'align-middle text-end' }
    ],
    ordering: false,
    order: [
        [0, 'asc']
    ],
    length: 100
});

function reload_datatablePrimaries() {
    datatable_primaries.ajax.reload(null, false);
}

$('#addprimary-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-primary.php',
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
                document.getElementById('addprimary-form').reset();
                bootstrap.Modal.getInstance(document.getElementById('static-addprimary')).hide();
                reload_datatablePrimaries();
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

function delete_primary(id) {
    Swal.fire({
        title: '¿Deseas eliminar la materia?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-primary.php',
                data: { primary: id, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        reload_datatablePrimaries();
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

function edit_primary(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-editprimary')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-primary.php',
        data: { primary: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('editprimary-form');
        },
        success: function (response) {
            $("#editprimary-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('editprimary-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#editprimary-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-primary.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-editprimary')).hide();
                reload_datatablePrimaries();
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