var itemsPage = 0;

var datatable_caja = $('#datatable-movements').DataTable({
    ajax: '../../controller/datatable-caja.php',
    columns: [
        { data: 'fecha', visible: false, searchable: true },
        { data: 'cantidad', className: 'align-middle text-start', searchable: true },
        { data: 'hora', className: 'align-middle text-center', searchable: true },
        { data: 'motivo', className: 'align-middle text-justify', searchable: true }
    ],
    ordering: false,
    order: [
        [1, 'desc']
    ],
    pageLength: 50,
    rowGroup: {
        dataSrc: 'fecha'
    }
});


datatable_caja.on('page', function () {
    itemsPage = datatable_caja.page();
});

function reload_datatableCaja() {
    datatable_caja.ajax.reload(function () {
        datatable_caja.page(itemsPage).draw(false);
    });
}


function get_init() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-amountInit')).show();

    $.ajax({
        type: 'POST',
        url: 'editform-monto.php',
        data: {},
        dataType: 'html',
        beforeSend: function () {
            animation_load('amountInit-form');
        },
        success: function (response) {
            $("#amountInit-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('amountInit-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}


$('#amountInit-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: 'crud-caja.php',
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
                bootstrap.Modal.getOrCreateInstance(document.getElementById('static-amountInit')).hide();
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


$('#adjustmentBox-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: 'crud-caja.php',
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
                reload_datatableCaja();
                document.getElementById('adjustmentBox-form').reset();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('static-adjustmentBox')).hide();
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