<?php
include('../model/db.php');
$list_products = products_for_combo();

$combo = $_POST['combo'] ?? '';
?>
<input type="hidden" name="id-combo" value="<?= $combo ?>">

<div class="d-grid">
    <label for="name-section-combo">Nombre de la Sección</label>
    <input type="text" name="name-section-combo" id="name-section-combo" placeholder="Ej. Plato Fuerte" required>
</div>

<div class="d-grid mt-2">
    <label for="dinamic-section-combo">Interactuación con la Sección</label>
    <select name="dinamic-section-combo" id="dinamic-section-combo">
        <option value="unico">Solo se puede seleccionar uno de los productos</option>
        <option value="multiple">Se pueden escoger mas de uno de los productos</option>
        <option value="default">Productos siempre incluidos en el combo</option>
    </select>
</div>

<div class="d-grid mt-2">
    <label for="instruction-section-combo">Instrucción</label>
    <textarea name="instruction-section-combo" id="instruction-section-combo" rows="3"
        placeholder="Instruccion sobre la seleccion de productos de la sección. Sino se ingresa una instrucción se pondra una predeterminada."></textarea>
</div>

<div class="container-fluid p-0 mt-4 mb-3">
    <div class="row justify-content-end">
        <div class="col-sm-12 col-lg-6">
            <div class="d-grid">
                <input type="search" id="search-product-armcombo" placeholder="Buscar producto">
            </div>
        </div>
    </div>
</div>

<div id="container-products-armcombo">
    <?php
    $pxc = [];
    if ($list_products) {
        foreach ($list_products as $p) {
            $cat_name = $p['name_category'] ?? 'Otros';
            $pxc[$cat_name][] = $p;
        }
    } else {
        echo '<div class="container-system-message">
            <i class="fi fi-br-restaurant"></i>
            <span>No hay productos registrados</span>
        </div>';
    }

    if ($pxc) {
        foreach ($pxc as $cat_name => $items) {
            ?>
            <div class="title-category-generic">
                <span class="text-capitalize"><?= htmlspecialchars($cat_name) ?></span>
            </div>

            <div class="container-fluid">
                <div class="row pt-2 cont-btn-check">
                    <?php foreach ($items as $i) {
                        $tipo = $i['type'];

                        if ($i['type'] == 'producto')  {
                            $identificador = $i['product'];
                        } else if ($i['type'] == 'variante') {
                            $identificador = $i['variant'];
                        } else {
                            $identificador = $i['extra'];
                        }

                        $unique = $tipo.'-'.$identificador;
                        ?>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-1 bc">
                            <input type="checkbox" class="btn-check" name="product-combo[]" id="check-<?= $unique ?>"
                                value="<?= $i['type'] ?>|<?= $i['product'] ?>|<?= $i['variant'] ?>|<?= $i['extra'] ?>" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="check-<?= $unique ?>">
                                <div class="container-icon"><i class="fi fi-br-hamburger-soda"></i></div>
                                <div class="col"><?= htmlspecialchars($i['name']) ?></div>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>


<script>
    (function () {
        const input = document.getElementById('search-product-armcombo');
        const cont = document.getElementById('container-products-armcombo');

        if (!input || !cont) return;

        function normalize(str) {
            return (str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();
        }

        function filterProducts() {
            const q = normalize(input.value);
            const sections = cont.querySelectorAll('.title-category-generic');

            sections.forEach(section => {
                const container = section.nextElementSibling;
                const group = container ? container.querySelector('.row') : null;

                if (!group) return;

                const categoryName = normalize(section.textContent);
                const categoryMatches = q !== '' && categoryName.includes(q);

                const items = group.querySelectorAll('.bc');
                let anyVisibleInCategory = false;

                items.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    const label = item.querySelector('label');
                    const name = normalize(label ? label.textContent : '');
                    const isChecked = checkbox ? checkbox.checked : false;

                    // Coincide si: no hay búsqueda, coincide el producto, coincide la categoría, o está marcado
                    const match = (q === '') ? true : (name.includes(q) || categoryMatches || isChecked);

                    item.style.display = match ? '' : 'none';

                    if (match) anyVisibleInCategory = true;
                });

                section.style.display = anyVisibleInCategory ? '' : 'none';
                group.style.display = anyVisibleInCategory ? '' : 'none';
            });
        }

        input.addEventListener('input', filterProducts);

        cont.addEventListener('change', (e) => {
            if (e.target && e.target.matches('input[type="checkbox"]')) {
                filterProducts();
            }
        });

        filterProducts();
    })();
</script>