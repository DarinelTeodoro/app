// Preloader
$(window).on('load', function () {
    $('#screen-preload').fadeOut(500, function () {
        $(this).remove();
    });
});

/***********************************************/
//Clase OBJECT son objetos que se pueden desabilitar
/***********************************************/
function show_alert(status, message) {
    document.getElementById('screen-alert').classList.add('visible');
    document.getElementById('tag-body').style.overflow = 'hidden';

    // Actualizar contenido
    $('#message-alert').html(message);
    $('#title-alert').html(status);
}

function hide_alert() {
    document.querySelectorAll('.object').forEach(btn => btn.disabled = false);

    document.getElementById('screen-alert').classList.remove('visible');
    document.getElementById('tag-body').style.overflow = 'auto';
}

function show_load() {
    document.querySelectorAll('.object').forEach(btn => btn.disabled = true);

    document.getElementById('screen-load').classList.add('visible');
    document.getElementById('tag-body').style.overflow = 'hidden';
}

function hide_load() {
    document.getElementById('screen-load').classList.remove('visible');
    document.getElementById('tag-body').style.overflow = 'auto';
}

function animation_load(container) {
    $('#' + container).html(
        `<div class="dot-wave">
            <div class="dot-wave__dot"></div>
            <div class="dot-wave__dot"></div>
            <div class="dot-wave__dot"></div>
            <div class="dot-wave__dot"></div>
        </div>`
    );
}

function message_error(container) {
    $('#'+container).html(
        `<div class="container-system-message">
            <i class="fi fi-br-not-found"></i>
            <span>Error al cargar el contenido</span>
        </div>`
    );
}

function load_section(path, container) {

    animation_load(container);

    $('#'+container).load(path, function (response, status, xhr) {
        if (status === "error") {
            message_error(container);
        }
    });
}

document.querySelectorAll('.modulo').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.modulo').forEach(btn => btn.disabled = false);
        this.disabled = true;

        bootstrap.Offcanvas.getInstance('#staticMenu').hide();
        let modulo = this.dataset.modulo;
        let path = modulo + '.php';

        load_section(path, 'container-main-home');
    });
});