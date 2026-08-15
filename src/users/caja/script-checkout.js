var itemsPage = 0;

var datatable_caja = $('#datatable-movements').DataTable({
    ajax: '../../controller/datatable-caja.php',
    columns: [
        { data: 'fecha', visible: false, searchable: true },
        { data: 'used', className: 'align-middle text-start', searchable: false },
        { data: 'cantidad', className: 'align-middle text-center', searchable: true },
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


$('#cutBox-form').submit(function (event) {
    event.preventDefault();

    function calcularTotal() {
        let mil = parseInt($('#mil').val()) || 0;
        let quinientos = parseInt($('#quinientos').val()) || 0;
        let docientos = parseInt($('#docientos').val()) || 0;
        let cien = parseInt($('#cien').val()) || 0;
        let cincuenta = parseInt($('#cincuenta').val()) || 0;
        let veinte = parseInt($('#veinte').val()) || 0;
        let pesos = parseFloat($('#monedas').val()) || 0;

        return mil + quinientos + docientos +
            cien + cincuenta + veinte +
            pesos;
    }

    let total = calcularTotal();
    let totalFormateado = total.toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // Usar SweetAlert2 para confirmación
    Swal.fire({
        title: 'Confirmar Corte de Caja',
        html: `
            <div style="text-align: left;">
                <span>Total Contado:</span>
                <p style="font-size: 24px; color: #2e7d32; font-weight: bold;">$${totalFormateado}</p>
                <hr>
                <div align="center">¿Estás seguro que la información es correcta?</div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
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
                        document.getElementById('cutBox-form').reset();
                        reload_datatableCaja();
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('static-cutBox')).hide();
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
});