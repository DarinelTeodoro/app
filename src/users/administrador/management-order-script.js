var itemsPage = 0;

var datatable_orders = $('#datatable-orders').DataTable({
    ajax: '../../controller/datatable-orders.php',
    columns: [
        { data: 'fecha', visible: false, searchable: true },
        { data: 'id', className: 'align-middle text-start', searchable: true },
        { data: 'status', className: 'align-middle text-center', searchable: true },
        { data: 'datetime', className: 'align-middle text-center', searchable: false },
        { data: 'details', className: 'align-middle text-center', searchable: false },
        { data: 'btn', className: 'align-middle text-end', searchable: false }
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


datatable_orders.on('page', function () {
    itemsPage = datatable_orders.page();
});

function reload_datatableOrders() {
    datatable_orders.ajax.reload(function () {
        datatable_orders.page(itemsPage).draw(false);
    });
}



function detail_order(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-managementorder')).show();

    $.ajax({
        type: 'POST',
        url: 'detail-order.php',
        data: { order: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('managementorder-form');
        },
        success: function (response) {
            $("#managementorder-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('managementorder-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}


function eliminar_item(orden, item) {
    Swal.fire({
        title: '¿Desea eliminar el producto de la orden?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'crud-order.php',
                data: { orden: orden, item: item, request: 'delete-item' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        detail_order(response.order);
                        reload_datatableOrders();
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



function delete_offer(orden, id) {
    Swal.fire({
        title: '¿Desea eliminar el descuento?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'crud-order.php',
                data: { orden: orden, offer: id, request: 'delete-offer' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        detail_order(response.order);
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