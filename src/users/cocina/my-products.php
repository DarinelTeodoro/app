<?php
include('../../model/db.php');

function consultar_productos()
{
    $conexion = new Conexion();
    $query_producto = $conexion->prepare("SELECT p.*,
                                          c.destination
                                   FROM product p
                                   LEFT JOIN category c ON p.category = c.id
                                   WHERE c.destination = 'cocina'
                                   ORDER BY p.product ASC");
    $query_producto->execute();
    $count_producto = $query_producto->rowCount();

    if ($count_producto > 0) {
        return $query_producto->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function consultar_variantes($idprod)
{
    $conexion = new Conexion();
    $query_variante = $conexion->prepare("SELECT * FROM variant WHERE product = :idprod ORDER BY increase ASC, variant ASC");
    $query_variante->bindParam(":idprod", $idprod);
    $query_variante->execute();
    $count_variante = $query_variante->rowCount();

    if ($count_variante > 0) {
        return $query_variante->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function consultar_extras()
{
    $conexion = new Conexion();
    $query_extra = $conexion->prepare("SELECT * FROM extra WHERE destination = 'cocina' ORDER BY extra ASC");
    $query_extra->execute();
    $count_extra = $query_extra->rowCount();

    if ($count_extra > 0) {
        return $query_extra->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

// Cargar toda la información sin filtros
?>

<div class="d-grid mb-3">
    <input type="search" id="search-varprod" name="search-varprod" placeholder="Buscar Producto/Variante/Extra"
        autocomplete="off">
    <!--<small class="text-muted mt-1" id="search-info">Mostrando todos los items</small>-->
</div>

<div id="text-no-reults"></div>



<div class="bg-warning p-2 fw-bold rounded mt-3 mb-1" id="title-seccion-combos">Combos</div>
<div class="d-grid gap-1" id="combos-container">
    <?php
    $datos_combo = combos();

    if ($datos_combo) {
        foreach ($datos_combo as $combo) {
            $check_c = isset($combo['available']) && $combo['available'] == 1 ? 'checked' : '';
            $combo_nombre = htmlspecialchars($combo['combo']);
            $combo_id = $combo['id'];

            echo '
            <div class="combo-wrapper" data-searchable="' . strtolower($combo_nombre) . '">
                <div class="combo-item border border-2 border-dark border-opacity-50 rounded p-2">
                    <div class="d-flex align-items-center justify-content-between" style="padding: 5px 10px;">
                        <span>' . $combo_nombre . '</span>
                        <div>
                            <input class="form-check-input switch-item" type="checkbox" role="switch" 
                                data-tipo="combo" 
                                data-id="' . $combo_id . '" 
                                id="switchcombo_' . $combo_id . '" ' . $check_c . '>
                        </div>    
                    </div>
                </div>
            </div>'; // Cierre extra-wrapper
        }
    } else {
        echo '<div class="text-center text-muted p-3 empty-message" id="combos-empty">No hay combos disponibles</div>';
    }
    ?>
</div>



<div class="bg-danger p-2 fw-bold text-light rounded mt-3 mb-1" id="title-seccion-productos">Productos y Variantes</div>
<div class="d-grid gap-1" id="productos-container">
    <?php
    $datos_producto = consultar_productos();

    if ($datos_producto) {
        foreach ($datos_producto as $producto) {
            $check_p = isset($producto['available']) && $producto['available'] == 1 ? 'checked' : '';
            $producto_nombre = htmlspecialchars($producto['product']);
            $producto_id = $producto['id'];

            echo '<div class="producto-wrapper" data-searchable="' . strtolower($producto_nombre) . '">';
            echo '<div class="producto-item border border-dark border-2 border-opacity-50 rounded d-grid gap-1 p-2" data-producto-id="' . $producto_id . '">';

            echo '<div class="d-flex align-items-center justify-content-between" style="padding: 5px 10px;">
                <span>' . $producto_nombre . '</span>
                <div>
                    <input class="form-check-input switch-item" type="checkbox" role="switch" 
                        data-tipo="producto" 
                        data-id="' . $producto_id . '" 
                        id="switchproducto_' . $producto_id . '" ' . $check_p . '>
                </div>    
            </div>';

            $datos_variantes = consultar_variantes($producto_id);
            if ($datos_variantes) {
                foreach ($datos_variantes as $variante) {
                    $check_v = isset($variante['available']) && $variante['available'] == 1 ? 'checked' : '';
                    $variante_nombre = htmlspecialchars($variante['variant']);
                    $variante_id = $variante['id'];

                    echo '<div class="variante-item d-flex align-items-center justify-content-between border border-2 border-dark border-opacity-25 rounded p-1 ps-2 pe-2" data-searchable="' . strtolower($variante_nombre) . '">
                        <li>' . $variante_nombre . '</li>
                        <div>
                            <input class="form-check-input switch-item" type="checkbox" role="switch" 
                                data-tipo="variante" 
                                data-id="' . $variante_id . '" 
                                id="switchvariante_' . $variante_id . '" ' . $check_v . '>
                        </div>  
                    </div>';
                }
            }
            echo '</div>'; // Cierre producto-item
            echo '</div>'; // Cierre producto-wrapper
        }
    } else {
        echo '<div class="text-center text-muted p-3 empty-message" id="productos-empty">No hay productos disponibles</div>';
    }
    ?>
</div>



<div class="bg-success p-2 fw-bold text-light rounded mt-3 mb-1" id="title-seccion-extras">Extras</div>
<div class="d-grid gap-1" id="extras-container">
    <?php
    $datos_extra = consultar_extras();

    if ($datos_extra) {
        foreach ($datos_extra as $extra) {
            $check_e = isset($extra['available']) && $extra['available'] == 1 ? 'checked' : '';
            $extra_nombre = htmlspecialchars($extra['extra']);
            $extra_id = $extra['id'];

            echo '<div class="extra-wrapper" data-searchable="' . strtolower($extra_nombre) . '">';
            echo '<div class="extra-item border border-2 border-dark border-opacity-50 rounded p-2">';
            echo '<div class="d-flex align-items-center justify-content-between" style="padding: 5px 10px;">
                ' . $extra_nombre . '
                <div>
                    <input class="form-check-input switch-item" type="checkbox" role="switch" 
                           data-tipo="extra" 
                           data-id="' . $extra_id . '" 
                           id="switchextra_' . $extra_id . '" ' . $check_e . '>
                </div>    
            </div>
        </div>';
            echo '</div>'; // Cierre extra-wrapper
        }
    } else {
        echo '<div class="text-center text-muted p-3 empty-message" id="extras-empty">No hay extras disponibles</div>';
    }
    ?>
</div>