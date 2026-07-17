<?php
include('../../model/db.php');
?>

<style>
    .submenu-menu .option-submenu {
        min-width: 132px;
    }
</style>

<div class="submenu-menu">
    <button type="button" class="option-submenu" data-section="inventory-adjustments" disabled>Inventario</button>
    <button type="button" class="option-submenu" data-section="inventory-ingredients">Ingredientes</button>
    <button type="button" class="option-submenu" data-section="inventory-primaries">Materias Primas</button>
    <button type="button" class="option-submenu" data-section="inventory-recipes">Recetas</button>
</div>

<div id="container-secondary"></div>

<script src="management-inventory-script.js"></script>