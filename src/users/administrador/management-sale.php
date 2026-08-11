<?php
date_default_timezone_set('America/Mexico_City');
$fecha = date('Y-m-d H:i:s');
?>
<style>
    .date-nav {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-nav button {
        height: 39.2px;
        width: 39.2px;
    }

    /*.date-nav button:hover {
        background: #e0e0e0;
    }

    .date-nav input[type="date"] {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
    }*/

    #resultado {
        margin-top: 20px;
    }
</style>

<div class="date-nav">
    <button type="button" id="btn-prev" class="btn-execute"><i class="fi fi-br-arrow-small-left"></i></button>
    <input type="date" id="input-fecha" value="<?= date('H') < 6 ? date('Y-m-d', strtotime($fecha . ' -1 day')) : date('Y-m-d') ?>">
    <button type="button" id="btn-next" class="btn-execute"><i class="fi fi-br-arrow-small-right"></i></button>
</div>

<div id="resultado">
    <div class="text-center bg-light p-3 fw-bold">Cargando datos del turno...</div>
</div>

<script>
    var inputFecha = document.getElementById('input-fecha');
    var btnPrev = document.getElementById('btn-prev');
    var btnNext = document.getElementById('btn-next');
    var resultado = document.getElementById('resultado');

    function toInputDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function fromInputDate(value) {
        const [y, m, d] = value.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function moverDias(cantidad) {
        const actual = fromInputDate(inputFecha.value);
        actual.setDate(actual.getDate() + cantidad);
        inputFecha.value = toInputDate(actual);
        cargarDatos(inputFecha.value);
    }

    btnPrev.addEventListener('click', () => moverDias(-1));
    btnNext.addEventListener('click', () => moverDias(1));

    inputFecha.addEventListener('change', () => cargarDatos(inputFecha.value));

    async function cargarDatos(fecha) {
        resultado.innerHTML = '<div class="text-center bg-light p-3 fw-bold">Cargando datos del turno...</div>';
        try {
            const res = await fetch(`../../controller/api-sales.php?fecha=${encodeURIComponent(fecha)}`);
            if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
            const html = await res.text();
            resultado.innerHTML = html;
        } catch (e) {
            resultado.innerHTML = `<span style="color:red">${e.message}</span>`;
        }
    }

    cargarDatos(inputFecha.value);
</script>