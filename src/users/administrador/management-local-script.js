function load_settings() {
    $.ajax({
        type: 'POST',
        url: '../../controller/local-get-settings.php',
        data: { request: 'info-tables' },
        dataType: 'json',
        success: function (detail) {
            if (detail && typeof detail.n_tables !== 'undefined') {
                $('#n-tables-local').html(detail.n_tables);
            } else {
                $('#n-tables-local').html('—');
                console.warn('Respuesta inesperada:', detail);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#n-tables-local').html('Error: ' + jqXHR.responseText);
        }
    });
}

load_settings();

function setting_tables() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-edittables')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-tables.php',
        data: {},
        dataType: 'html',
        beforeSend: function () {
            animation_load('edittables-form');
        },
        success: function (response) {
            $("#edittables-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('edittables-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}


$('#edittables-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-settings.php',
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
                load_settings();
                bootstrap.Modal.getInstance(document.getElementById('static-edittables')).hide();
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