$('#addcombo-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-combo.php',
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
                document.getElementById('addcombo-form').reset();
                bootstrap.Modal.getInstance(document.getElementById('static-addcombo')).hide();
                load_section('../../controller/manage-list-combos.php', 'container-secondary');
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

function delete_combo(id) {
    Swal.fire({
        title: '¿Deseas eliminar el combo?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-combo.php',
                data: { combo: id, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        load_section('../../controller/manage-list-combos.php', 'container-secondary');
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

function edit_combo(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-editcombo')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-combo.php',
        data: { combo: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('editcombo-form');
        },
        success: function (response) {
            $("#editcombo-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('editcombo-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#editcombo-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-combo.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-editcombo')).hide();
                load_section('../../controller/manage-list-combos.php', 'container-secondary');
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

function config_combo(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-detailcombo')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/info-combo-admin.php',
        data: { combo: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('container-detailcombo');
        },
        success: function (response) {
            $("#container-detailcombo").html(response);
        },
        error: function (xhr, status, error) {
            message_error('container-detailcombo');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

function arm_combo(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-armcombo')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/arm-combo-admin.php',
        data: { combo: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('container-armcombo');
        },
        success: function (response) {
            $("#container-armcombo").html(response);
        },
        error: function (xhr, status, error) {
            message_error('container-armcombo');
        },
        /*complete: function () {
            animation_load('edituser-form');
        }*/
    });
}


$('#armcombo-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-combo.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-armcombo')).hide();
                config_combo(response.id);
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


function delete_section(id) {
    Swal.fire({
        title: '¿Deseas eliminar la sección?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-combo.php',
                data: { section: id, request: 'delete-section' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        config_combo(response.id);
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


function product_plus(id) {
    $.ajax({
        type: 'POST',
        url: '../../controller/crud-combo.php',
        data: { item: id, request: 'qty-up' },
        dataType: 'json',
        beforeSend: function () {
            show_load();
        },
        success: function (response) {
            if (response.status === 201) {
                $('#qty-'+id).html(response.qty);
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
            document.querySelectorAll('.object').forEach(btn => btn.disabled = false);
        }
    });
}

function product_less(id) {
    $.ajax({
        type: 'POST',
        url: '../../controller/crud-combo.php',
        data: { item: id, request: 'qty-down' },
        dataType: 'json',
        beforeSend: function () {
            show_load();
        },
        success: function (response) {
            if (response.status === 201) {
                $('#qty-'+id).html(response.qty);
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
            document.querySelectorAll('.object').forEach(btn => btn.disabled = false);
        }
    });
}