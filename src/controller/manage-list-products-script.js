document.addEventListener('change', e => {
    if (e.target.id === 'img-product') {
        img_product(e, 'label-img-addproduct');
    }
});

function img_product(e, label) {
    const cont_img_product = document.getElementById(label);
    const input = e.target;
    const file = input.files?.[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            cont_img_product.style.background =
                `rgb(0, 0, 0, 0.2) url(${ev.target.result}) center center / cover no-repeat`;
        };
        reader.readAsDataURL(file);
    } else {
        cont_img_product.style.background =
            `rgb(0, 0, 0, 0.2) url('../../../files/img_products/default.webp') center center / cover no-repeat`;
    }
}

$('#addproduct-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-product.php',
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
                document.getElementById('addproduct-form').reset();
                bootstrap.Modal.getInstance(document.getElementById('static-addproduct')).hide();
                load_section('../../controller/manage-list-products.php', 'container-secondary');
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

function delete_product(id) {
    Swal.fire({
        title: '¿Deseas eliminar el producto?',
        icon: 'question',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '../../controller/crud-product.php',
                data: { product: id, request: 'delete' },
                dataType: 'json',
                beforeSend: function () {
                    show_load();
                },
                success: function (response) {
                    if (response.status === 201) {
                        show_alert(response.title, response.message);
                        load_section('../../controller/manage-list-products.php', 'container-secondary');
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

function edit_product(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('static-editproduct')).show();

    $.ajax({
        type: 'POST',
        url: '../../controller/editform-product.php',
        data: { product: id },
        dataType: 'html',
        beforeSend: function () {
            animation_load('editproduct-form');
        },
        success: function (response) {
            $("#editproduct-form").html(response);
        },
        error: function (xhr, status, error) {
            message_error('editproduct-form');
        }/*,
        complete: function () {
            animation_load('edituser-form');
        }*/
    });
}

$('#editproduct-form').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: '../../controller/crud-product.php',
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
                bootstrap.Modal.getInstance(document.getElementById('static-editproduct')).hide();
                load_section('../../controller/manage-list-products.php', 'container-secondary');
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