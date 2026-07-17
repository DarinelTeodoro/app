$('#login-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: 'src/controller/access-user.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            show_load();
        },
        success: function (response) {
            if (response.status === 'ACCGRANTED01') {
                $('#key-user').val('');
                $('#key-password').val('');
                window.location.href = response.path;
            } else {
                show_alert(response.status, response.message);
                $('#key-user').val('');
                $('#key-password').val('');
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