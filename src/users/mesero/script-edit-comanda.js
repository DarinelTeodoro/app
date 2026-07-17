const INTERVALO_MS = 10000; // 10 segundos
const productosAnteriores = new Map(); // clave -> JSON string del producto (para detectar cambios)
let ultimoSnapshot = '';

let funcionesPorTipo = {};

let carrito = [];
let extrasDisponibles = []; // se llena una vez, en load_data()
let configActual = null;    // estado temporal mientras el modal de extras está abierto

let variantesPorProducto = {}; // se llena en load_data()

let combosPorProducto = {};
let comboProdActual = null;

// Mapa real: nombre (string) -> función JS de verdad
const ACCIONES = {
    add_product: add_product,
    select_variante: select_variante,
    select_combo: select_combo
};


// mandar a traer informacion de la base d edatos
async function load_data() {
    try {
        const respuesta = await fetch('../../controller/api-products.php');
        const resultado = await respuesta.json();

        if (resultado.success) {
            funcionesPorTipo = resultado.functions || {};
            extrasDisponibles = resultado.extras || [];
            variantesPorProducto = resultado.variants || {};
            combosPorProducto = resultado.combos || {};
            update_cards(resultado.data);
        } else {
            console.error('Error del servidor:', resultado.error);
        }
    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}



// Clave única por fila: id + nombre del producto.
// Se usa el nombre además del id porque un producto con variantes genera
// varias filas con el MISMO id (una por variante), y el nombre es lo que
// las distingue (ej. "Hamburguesa (Chica)" vs "Hamburguesa (Grande)").
function get_clave(producto) {
    return `${producto.id}::${producto.product}`;
}


// actualizar cards
function update_cards(productos) {
    const snapshotActual = JSON.stringify(productos);
    if (snapshotActual === ultimoSnapshot) return;
    ultimoSnapshot = snapshotActual;

    const contenedor = document.getElementById('menu-for-clients');

    // Si no hay productos, se muestra el mensaje y no se sigue construyendo nada
    if (productos.length === 0) {
        contenedor.innerHTML = `
            <div class="message-no-items" align="center">
                <div class="mb-2"><i class="fi fi-br-plate-utensils"></i></div>
                <span>No hay productos disponibles en este momento</span>
            </div>
        `;
        productosAnteriores.clear(); // se limpia el registro, así si vuelven productos se crean como nuevos
        return;
    }

    // Si había mensaje de "sin productos" de un poll anterior, se quita antes de construir las tarjetas
    const mensajeVacio = contenedor.querySelector('.message-no-items');
    if (mensajeVacio) mensajeVacio.remove();

    const clavesNuevas = new Set();

    productos.forEach(producto => {
        const clave = get_clave(producto);
        clavesNuevas.add(clave);
        const productoJSON = JSON.stringify(producto);

        const seccion = get_or_create_category_section(contenedor, producto.category_id, producto.category);
        const grid = seccion.querySelector('.cards-grid');

        if (productosAnteriores.get(clave) === productoJSON) return;

        let carta = contenedor.querySelector(`[data-key="${clave}"]`);

        if (!carta) {
            carta = document.createElement('div');
            carta.dataset.key = clave;
            grid.appendChild(carta);
        } else if (carta.parentElement !== grid) {
            grid.appendChild(carta);
        }

        info_card(carta, producto);
        productosAnteriores.set(clave, productoJSON);
    });

    // Eliminar tarjetas de productos que ya no vienen en la respuesta
    productosAnteriores.forEach((_, clave) => {
        if (!clavesNuevas.has(clave)) {
            const carta = contenedor.querySelector(`[data-key="${clave}"]`);
            if (carta) carta.remove();
            productosAnteriores.delete(clave);
        }
    });

    // Eliminar secciones de categoría que se quedaron sin productos
    contenedor.querySelectorAll('.category-section').forEach(seccion => {
        if (seccion.querySelector('.cards-grid').children.length === 0) {
            seccion.remove();
        }
    });
}


// obtener informacion o crear categorias
function get_or_create_category_section(contenedor, categoryId, categoryName) {
    let seccion = contenedor.querySelector(`[data-category-id="${categoryId}"]`);
    if (!seccion) {
        seccion = document.createElement('section');
        seccion.className = 'category-section';
        seccion.dataset.categoryId = categoryId;
        seccion.innerHTML = `<h2 class="category-title"></h2><div class="cards-grid"></div>`;
        contenedor.appendChild(seccion);
    }
    seccion.querySelector('.category-title').textContent = categoryName;
    return seccion;
}


// imprimir datos del producto
function info_card(div, producto) {
    div.className = producto.available == 0 ? 'card-product-disabled' : 'card-product';

    div.innerHTML = `
    <div class="img-product" style="background: rgb(0, 0, 0, 0.1) url('../../../files/img_products/${producto.img}') center center / cover no-repeat;">
        <span>${producto.available == 0 ? 'Agotado' : ''}</span>
    </div>
    <div class="info-product">
        <span>${escapeHTML(producto.product)}</span>
        <strong>$${escapeHTML(producto.price)}</strong>
    </div>
    `;

    // poner la funcion
    if (producto.available == 0) {
        div.onclick = null;
    } else {
        div.onclick = () => {
            const nombreFuncion = funcionesPorTipo[producto.type];
            const accion = ACCIONES[nombreFuncion];

            if (typeof accion === 'function') {
                accion(producto);
            } else {
                console.error('No hay acción definida para el tipo:', producto.type);
            }
        };
    }
}

// agregar comillas
function escapeHTML(texto) {
    const span = document.createElement('span');
    span.textContent = texto ?? '';
    return span.innerHTML;
}


// Iniciar recarga cada 10s
load_data();
setInterval(load_data, INTERVALO_MS);



/**********************************************************************************************************/
function generar_id() {
    return 'item-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9);
}

function add_product(producto) {
    abrir_modal_extras(producto, null); // null = es un item nuevo, no una edición
}

function abrir_modal_extras(producto, itemExistente) {
    configActual = {
        producto: producto,
        cartId: itemExistente ? itemExistente.cartId : null,
        extrasSeleccionados: itemExistente ? [...itemExistente.extras] : [],
        qty: itemExistente ? itemExistente.qty : 1
    };

    document.getElementById('extras-product-name').textContent = producto.product;
    document.getElementById('extras-comment').value = itemExistente ? itemExistente.comment : '';

    render_lista_extras();

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('static-extras'));
    modal.show();
}

function obtener_extras_filtrados(producto) {
    if (producto.type === 'combo') {
        return extrasDisponibles;
    }
    return extrasDisponibles.filter(extra => extra.destination === producto.destination);
}

function render_lista_extras() {
    const contenedor = document.getElementById('extras-list');
    const extrasFiltrados = obtener_extras_filtrados(configActual.producto);

    if (extrasFiltrados.length === 0) {
        contenedor.innerHTML = `
            <div class="message-no-items" align="center">
                <span>No hay extras disponibles</span>
            </div>
        `;
        document.getElementById('extras-total-monto').textContent = '$0.00';
        return;
    }

    contenedor.innerHTML = extrasFiltrados.map(extra => {
        const seleccionado = configActual.extrasSeleccionados.find(e => e.id === extra.id);
        const cantidad = seleccionado ? seleccionado.qty : 0;
        const subtotalExtra = Number(extra.price) * cantidad;

        return `
        <div class="extra-item" data-extra-id="${extra.id}">
            <div class="extra-item-info">
                <span>${escapeHTML(extra.extra)}</span>
                <small class="text-muted">($${escapeHTML(extra.price)} c/u)</small>
            </div>
            <div class="extra-item-subtotal">$${subtotalExtra.toFixed(2)}</div>
            <div class="extra-item-qty">
                <button type="button" class="btn-qty" data-action="restar-extra"><i class="fi fi-br-minus-small"></i></button>
                <span class="extra-qty-valor">${cantidad}</span>
                <button type="button" class="btn-qty" data-action="sumar-extra"><i class="fi fi-br-plus-small"></i></button>
            </div>
        </div>
        `;
    }).join('');

    const totalExtras = configActual.extrasSeleccionados.reduce((sum, e) => sum + Number(e.price) * e.qty, 0);
    document.getElementById('extras-total-monto').textContent = `$${totalExtras.toFixed(2)}`;
}

function actualizar_resumen_extras() {
    const totalExtras = configActual.extrasSeleccionados.reduce((sum, e) => sum + Number(e.price) * e.qty, 0);
    const totalUnidad = Number(configActual.producto.price) + totalExtras;

    document.getElementById('extras-total-monto').textContent = `$${totalExtras.toFixed(2)}`;
    document.getElementById('extras-total-unidad').textContent = `$${totalUnidad.toFixed(2)}`;
}

document.getElementById('extras-list').addEventListener('click', (e) => {
    const boton = e.target.closest('button[data-action]');
    if (!boton) return;

    const fila = boton.closest('.extra-item');
    const extraId = Number(fila.dataset.extraId);
    const delta = boton.dataset.action === 'sumar-extra' ? 1 : -1;

    cambiar_cantidad_extra(extraId, delta);
});

function cambiar_cantidad_extra(extraId, delta) {
    const extra = extrasDisponibles.find(e => e.id === extraId);
    let item = configActual.extrasSeleccionados.find(e => e.id === extraId);

    if (!item) {
        if (delta < 0) return; // no puede bajar de 0 si todavía no existe en la lista
        item = { id: extra.id, extra: extra.extra, price: extra.price, qty: 0, destination: extra.destination };
        configActual.extrasSeleccionados.push(item);
    }

    item.qty += delta;

    if (item.qty <= 0) {
        configActual.extrasSeleccionados = configActual.extrasSeleccionados.filter(e => e.id !== extraId);
    }

    render_lista_extras(); // se reconstruye toda la lista para reflejar el nuevo contador
}

document.getElementById('btn-confirm-extras').addEventListener('click', () => {
    const comentario = document.getElementById('extras-comment').value.trim();
    guardar_item_configurado(configActual, comentario);

    bootstrap.Modal.getInstance(document.getElementById('static-extras')).hide();
    render_carrito();
    abrir_modal_orden();
});

function guardar_item_configurado(config, comentario) {
    const totalExtras = config.extrasSeleccionados.reduce((sum, e) => sum + Number(e.price) * e.qty, 0);
    const precioUnitario = Number(config.producto.price) + totalExtras;

    if (config.cartId) {
        const item = carrito.find(i => i.cartId === config.cartId);
        item.extras = config.extrasSeleccionados;
        item.comment = comentario;
        item.qty = config.qty;
        item.unitPrice = precioUnitario;
    } else {
        carrito.push({
            //cartId: crypto.randomUUID(),
            cartId: generar_id(),
            id: config.producto.id,
            type: config.producto.type,
            product: config.producto.product,
            variant_id: config.producto.variant_id || null,
            destination: config.producto.destination,
            basePrice: Number(config.producto.price),
            comboItems: config.producto.comboItems || [],
            extras: config.extrasSeleccionados,
            comment: comentario,
            qty: config.qty,
            unitPrice: precioUnitario
        });
    }
}

function render_carrito() {
    const contenedor = document.getElementById('itemized-order');

    if (carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="message-no-items">
                <div class="mb-2"><i class="fi fi-br-plate-utensils"></i></div>
                <span>No hay productos agregados</span>
            </div>
        `;
        document.getElementById('total-order').innerHTML = '0.00';
        return;
    }

    let total = 0;
    const filas = carrito.map(item => {
        const subtotal = item.unitPrice * item.qty;
        total += subtotal;

        const extrasSubtotal = item.extras.reduce((sum, e) => sum + Number(e.price) * e.qty, 0);

        const comboTexto = (() => {
            if (!item.comboItems || item.comboItems.length === 0) return '';

            // Agrupar items por group_id
            const grupos = {};
            item.comboItems.forEach(ci => {
                if (!grupos[ci.group_id]) {
                    grupos[ci.group_id] = { group_name: ci.group_name, items: [] };
                }
                grupos[ci.group_id].items.push(ci);
            });

            return Object.values(grupos).map(grupo => `
                <div>
                    <small class="text-muted">${escapeHTML(grupo.group_name)}:</small>
                    <ul class="extras-texto">
                        ${grupo.items.map(ci => `<li>${escapeHTML(ci.name)} x ${ci.qty}</li>`).join('')}
                    </ul>
                </div>
            `).join('');
        })();

        const extrasTexto = item.extras
            .map(e => {
                const subtotalExtra = Number(e.price) * e.qty;
                return `<li> + ${escapeHTML(e.extra)} x ${e.qty} <span class="text-muted ms-1">($${subtotalExtra.toFixed(2)})</span></li>`;
            })
            .join('');

        return `
        <div class="item-order" data-cart-id="${item.cartId}">
            <div class="item-order-info">
                <div align="start"><span>${escapeHTML(item.product)}</span><span class="text-muted ms-1 fw-light">($${item.basePrice.toFixed(2)})</span></div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn-edit" data-action="editar"><i class="fi fi-br-pencil"></i></button>
                    <button type="button" class="btn-remove" data-action="eliminar"><i class="fi fi-br-trash"></i></button>
                </div>
            </div>
            <div class="item-order-extras">
                ${comboTexto ? `<div class="combo-items-texto">${comboTexto}</div>` : ''}
                ${extrasTexto ? `
                    <div>
                        <ul class="extras-texto" style="list-style: none;">${extrasTexto}</ul>
                    </div>
                ` : ''}
                ${item.comment ? `<p class="comentario-texto">"${escapeHTML(item.comment)}"</p>` : ''}
            </div>
            <div class="item-order-prices">
                <div>
                    <small>$${item.unitPrice.toFixed(2)} c/u</small>
                </div>
                <div class="item-order-qty">
                    <button type="button" class="btn-qty" data-action="restar"><i class="fi fi-br-minus-small"></i></button>
                    <span>${item.qty}</span>
                    <button type="button" class="btn-qty" data-action="sumar"><i class="fi fi-br-plus-small"></i></button>
                </div>
                <div class="item-order-subtotal">$${subtotal.toFixed(2)}</div>
            </div>
        </div>
        `;
    }).join('');

    contenedor.innerHTML = filas;
    document.getElementById('total-order').innerHTML = total.toFixed(2);
}

document.getElementById('itemized-order').addEventListener('click', (e) => {
    const boton = e.target.closest('button[data-action]');
    if (!boton) return;

    const fila = boton.closest('.item-order');
    const cartId = fila.dataset.cartId;
    const item = carrito.find(i => i.cartId === cartId);
    const accion = boton.dataset.action;

    if (accion === 'sumar') { item.qty += 1; render_carrito(); }
    if (accion === 'restar') {
        item.qty -= 1;
        if (item.qty <= 0) quitar_del_carrito(cartId); else render_carrito();
    }
    if (accion === 'eliminar') quitar_del_carrito(cartId);
    if (accion === 'editar') {
        // Reconstruye un "producto" mínimo a partir del item guardado, para reabrir el modal
        const productoBase = {
            id: item.id,
            type: item.type,
            product: item.product,
            price: item.basePrice,
            destination: item.destination,
            comboItems: item.comboItems
        };
        abrir_modal_extras(productoBase, item);
    }
});

function quitar_del_carrito(cartId) {
    carrito = carrito.filter(item => item.cartId !== cartId);
    render_carrito();
}

function abrir_modal_orden() {
    const myOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('static-order'));
    myOffcanvas.show();
}


function select_variante(producto) {
    abrir_modal_variantes(producto);
}

function abrir_modal_variantes(producto) {
    const opciones = variantesPorProducto[producto.id];

    if (!opciones) {
        console.error('No hay variantes registradas para este producto:', producto.id);
        return;
    }

    document.getElementById('variantes-product-name').textContent = producto.product;

    const contenedor = document.getElementById('variantes-list');
    contenedor.innerHTML = opciones.map(variante => `
        <button type="button" class="variante-item" data-variant-id="${variante.id}">
            <span>${escapeHTML(variante.variant)}</span>
            <span class="ms-1">+ ($${escapeHTML(variante.price)})</span>
        </button>
    `).join('');

    contenedor.querySelectorAll('.variante-item').forEach(boton => {
        boton.addEventListener('click', () => {
            const variantId = Number(boton.dataset.variantId);
            const variante = opciones.find(v => v.id === variantId);
            seleccionar_variante(producto, variante);
        });
    });

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('static-variantes'));
    modal.show();
}

function seleccionar_variante(producto, variante) {
    bootstrap.Modal.getInstance(document.getElementById('static-variantes')).hide();

    const productoVariante = {
        id: producto.id,
        variant_id: variante.id,
        type: 'variante',
        product: `${producto.product} (${variante.variant})`,
        price: (parseFloat(variante.price) + parseFloat(producto.price)),
        destination: producto.destination
    };

    abrir_modal_extras(productoVariante, null);
}







function select_combo(producto) {
    abrir_modal_combo(producto);
}

function abrir_modal_combo(producto) {
    const grupos = combosPorProducto[producto.id];
    comboProdActual = producto;

    if (!grupos || grupos.length === 0) {
        show_alert('Combo no Disponible', 'No hay grupos para este combo:', producto.id);
        return;
    }

    document.getElementById('combo-product-name').textContent = producto.product;

    const contenedor = document.getElementById('combo-groups-list');
    contenedor.innerHTML = grupos.map(grupo => {
        const esDefault = grupo.selection_type === 'default';
        const esUnico = grupo.selection_type === 'unico';
        const esMultiple = grupo.selection_type === 'multiple';

        const items = grupo.products.map(item => {
            if (esDefault) {
                return `
                <div class="combo-item" data-group-id="${grupo.group_id}" data-item-id="${item.id}" data-qty-default="${item.qty}">
                    <span class="combo-item-name">${escapeHTML(item.name)}</span>
                    <div class="combo-item-qty">
                        <button type="button" class="btn-qty" data-action="restar-combo"><i class="fi fi-br-minus-small"></i></button>
                        <span class="combo-qty-valor">${item.qty}</span>
                        <button type="button" class="btn-qty" data-action="sumar-combo"><i class="fi fi-br-plus-small"></i></button>
                    </div>
                </div>`;
            }

            if (esUnico) {
                return `
                <div class="combo-item" data-group-id="${grupo.group_id}" data-item-id="${item.id}" data-qty-default="${item.qty}">
                    <div class="combo-item-label">
                        <input type="radio" class="btn-check" name="combo-group-${grupo.group_id}" id="combo-radio-${grupo.group_id}-${item.id}" value="${item.id}" autocomplete="off">
                        <label class="btn btn-outline-primary" for="combo-radio-${grupo.group_id}-${item.id}">
                            ${escapeHTML(item.name)}
                        </label>
                    </div>
                    <div class="combo-item-qty" style="display:none;">
                        <button type="button" class="btn-qty" data-action="restar-combo"><i class="fi fi-br-minus-small"></i></button>
                        <span class="combo-qty-valor">${item.qty}</span>
                        <button type="button" class="btn-qty" data-action="sumar-combo"><i class="fi fi-br-plus-small"></i></button>
                    </div>
                </div>`;
            }

            if (esMultiple) {
                return `
                <div class="combo-item" data-group-id="${grupo.group_id}" data-item-id="${item.id}" data-qty-default="${item.qty}">
                    <div class="combo-item-label">
                        <input type="checkbox" class="btn-check" name="combo-group-${grupo.group_id}" id="combo-check-${grupo.group_id}-${item.id}" value="${item.id}" autocomplete="off">
                        <label class="btn btn-outline-primary" for="combo-check-${grupo.group_id}-${item.id}">
                            ${escapeHTML(item.name)}
                        </label>
                    </div>
                    <div class="combo-item-qty" style="display:none;">
                        <button type="button" class="btn-qty" data-action="restar-combo"><i class="fi fi-br-minus-small"></i></button>
                        <span class="combo-qty-valor">${item.qty}</span>
                        <button type="button" class="btn-qty" data-action="sumar-combo"><i class="fi fi-br-plus-small"></i></button>
                    </div>
                </div>`;
            }
        }).join('');

        return `
        <div class="combo-group" data-group-id="${grupo.group_id}" data-selection-type="${grupo.selection_type}">
            <div class="combo-group-header">
                <strong>${escapeHTML(grupo.group_name)}</strong>
                <small class="text-muted">${escapeHTML(grupo.instruction)}</small>
            </div>
            <div class="combo-group-items">${items}</div>
        </div>`;
    }).join('');

    // Mostrar/ocultar +/- al marcar radio o checkbox
    contenedor.querySelectorAll('input[type=radio], input[type=checkbox]').forEach(input => {
        input.addEventListener('change', () => {
            const grupoEl = input.closest('.combo-group');
            const tipo = grupoEl.dataset.selectionType;

            if (tipo === 'unico') {
                // Ocultar todos y mostrar solo el seleccionado
                grupoEl.querySelectorAll('.combo-item-qty').forEach(q => q.style.display = 'none');
                grupoEl.querySelectorAll('.combo-qty-valor').forEach((span, _, arr) => {
                    const itemEl = span.closest('.combo-item');
                    span.textContent = itemEl.dataset.qtyDefault;
                });
                if (input.checked) {
                    input.closest('.combo-item').querySelector('.combo-item-qty').style.display = 'flex';
                }
            }

            if (tipo === 'multiple') {
                const itemEl = input.closest('.combo-item');
                const qtyEl = itemEl.querySelector('.combo-item-qty');
                qtyEl.style.display = input.checked ? 'flex' : 'none';
                if (!input.checked) {
                    itemEl.querySelector('.combo-qty-valor').textContent = itemEl.dataset.qtyDefault;
                }
            }
        });
    });

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('static-combo'));
    modal.show();
}

document.getElementById('combo-groups-list').addEventListener('click', (e) => {
    const boton = e.target.closest('button[data-action]');
    if (!boton) return;
    if (boton.dataset.action !== 'sumar-combo' && boton.dataset.action !== 'restar-combo') return;

    const itemEl = boton.closest('.combo-item');
    const qtyEl = itemEl.querySelector('.combo-qty-valor');
    let qty = parseInt(qtyEl.textContent);

    if (boton.dataset.action === 'sumar-combo') qty += 1;
    if (boton.dataset.action === 'restar-combo' && qty > 1) qty -= 1;

    qtyEl.textContent = qty;
});

document.getElementById('btn-confirm-combo').addEventListener('click', () => {
    const grupos = combosPorProducto[comboProdActual.id];
    const contenedor = document.getElementById('combo-groups-list');
    const itemsSeleccionados = [];
    const faltantes = []; // <-- acumula todos los grupos con error

    for (const grupo of grupos) {
        const grupoEl = contenedor.querySelector(`.combo-group[data-group-id="${grupo.group_id}"]`);

        if (grupo.selection_type === 'default') {
            grupoEl.querySelectorAll('.combo-item').forEach(itemEl => {
                const itemId = Number(itemEl.dataset.itemId);
                const qty = parseInt(itemEl.querySelector('.combo-qty-valor').textContent);
                const itemData = grupo.products.find(p => p.id === itemId);
                itemsSeleccionados.push({ ...itemData, qty, group_id: grupo.group_id, group_name: grupo.group_name });
            });
        }

        if (grupo.selection_type === 'unico') {
            const radio = grupoEl.querySelector('input[type=radio]:checked');
            if (!radio) {
                faltantes.push(grupo.group_name); // <-- acumula en vez de break
            } else {
                const itemEl = radio.closest('.combo-item');
                const itemId = Number(itemEl.dataset.itemId);
                const qty = parseInt(itemEl.querySelector('.combo-qty-valor').textContent);
                const itemData = grupo.products.find(p => p.id === itemId);
                itemsSeleccionados.push({ ...itemData, qty, group_id: grupo.group_id, group_name: grupo.group_name });
            }
        }

        if (grupo.selection_type === 'multiple') {
            const checks = grupoEl.querySelectorAll('input[type=checkbox]:checked');
            if (checks.length === 0) {
                faltantes.push(grupo.group_name); // <-- acumula en vez de break
            } else {
                checks.forEach(chk => {
                    const itemEl = chk.closest('.combo-item');
                    const itemId = Number(itemEl.dataset.itemId);
                    const qty = parseInt(itemEl.querySelector('.combo-qty-valor').textContent);
                    const itemData = grupo.products.find(p => p.id === itemId);
                    itemsSeleccionados.push({ ...itemData, qty, group_id: grupo.group_id, group_name: grupo.group_name });
                });
            }
        }
    }

    // Si hay faltantes, mostrar todos juntos
    if (faltantes.length > 0) {
        const lista = faltantes.map(nombre => `<li class="p-0" style="list-style-type: none;">• ${nombre}</li>`).join('');
        show_alert('Atención', `No ha seleccionado en:<ul class="p-0">${lista}</ul>`);
        return;
    }

    comboProdActual.comboItems = itemsSeleccionados;
    bootstrap.Modal.getInstance(document.getElementById('static-combo')).hide();
    abrir_modal_extras(comboProdActual, null);
});







// NUEVO PRODUCTO
document.getElementById('btn-add-product').addEventListener('click', () => {
    document.getElementById('new-product-name').value = '';
    document.getElementById('new-product-destination').value = 'barra';
    document.getElementById('new-product-price').value = '';

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('static-new-product'));
    modal.show();
});

document.getElementById('btn-confirm-new-product').addEventListener('click', () => {
    const nombre = document.getElementById('new-product-name').value.trim();
    const destino = document.getElementById('new-product-destination').value;
    const precio = parseFloat(document.getElementById('new-product-price').value);

    if (!nombre) {
        show_alert('Dato necesario', 'Escribe el nombre del producto');
        return;
    }
    if (isNaN(precio) || precio < 0) {
        show_alert('Dato necesario', 'Escribe un costo válido');
        return;
    }

    const productoNuevo = {
        id: 'manual-' + generar_id(), // id ficticio, no existe en BD
        type: 'especial',             // se trata como producto normal para reusar el filtro de extras
        product: nombre,
        price: precio,
        destination: destino
    };

    bootstrap.Modal.getInstance(document.getElementById('static-new-product')).hide();
    abrir_modal_extras(productoNuevo, null);
});






$('#static-order').submit(function (event) {
    event.preventDefault();

    var formData = new FormData(this);
    formData.append('carrito', JSON.stringify(carrito));

    $.ajax({
        type: 'POST',
        url: 'crud-order.php',
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

                carrito = [];
                render_carrito();

                document.getElementById('static-order').reset();
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