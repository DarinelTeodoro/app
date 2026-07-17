document.querySelectorAll('.modulo-filtro').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.modulo-filtro').forEach(btn => btn.disabled = false);
        this.disabled = true;

        const filtro = this.dataset.filtro;
        window.dispatchEvent(new CustomEvent('filtro-comandas', { detail: filtro }));
    });
});