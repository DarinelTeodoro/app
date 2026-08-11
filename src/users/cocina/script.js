function order_complete(id) {
    $.ajax({
        type: 'POST',
        url: '../../controller/order-complete.php',
        data: { order_id: id, destination: 'cocina' },
        dataType: 'json',
        success: function (detail) {
            if(detail.status != 201) {
                show_alert(detail.title, detail.message)
            } else {
                window.dispatchEvent(new CustomEvent('filtro-comandas', { detail: 'pendiente' }));
            }
        },
        error: function () {
            show_alert('Error', 'No se pudo completar la orden')
        }
    });
}