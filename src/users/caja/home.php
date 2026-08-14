<?php
session_start();
$root = '../../../';
include($root . 'cdn.html');
include('../../model/db.php');
$app_name = $_SESSION['app-name'];

if (empty($_SESSION['data-useractive'])) {
    header('Location: ' . $root . 'index.php');
} else {
    $id_user = $_SESSION['data-useractive'];
    $user = search_userid($id_user);

    if ($_SESSION['rol-useractive'] == 'administrador') {
        header('Location: ../administrador/home.php');
    } else if ($_SESSION['rol-useractive'] == 'mesero') {
        header('Location: ../mesero/home.php');
    } else if ($_SESSION['rol-useractive'] == 'barra') {
        header('Location: ../barra/home.php');
    } else if ($_SESSION['rol-useractive'] == 'cocina') {
        header('Location: ../cocina/home.php');
    }
}

if (isset($_POST['logout-session'])) {
    session_destroy();
    header('Location: ' . $root . 'index.php');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= $root ?>favicon.ico">
    <title><?= $app_name ?> - Caja</title>
    <link href="<?= $root ?>style.css" rel="stylesheet">
    <link href="<?= $root ?>style-loader.css" rel="stylesheet">
    <link href="<?= $root ?>style-alert.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body id="tag-body">

    <!--DISEÑO INDEX (CONTENEDOR PRINCIPAL)-->
    <div class="fixed-top system-navbar">
        <div class="d-flex align-items-center">
            <a href="home.php"><img src="<?= $root ?>files/rabbit-cajero.png" class="navbar-logo"></a>
            <div class="lh-15">
                <div class="ms-1"><span class="text-headline">Bienvenido</span></div>
                <div class="ms-1"><span class="name-user"><?= $user['name'] ?></span></div>
            </div>
        </div>
        <div>
            <i class="bi bi-list" data-bs-toggle="offcanvas" data-bs-target="#staticMenu"
                aria-controls="staticMenu"></i>
        </div>
    </div>

    <!--MENU-->
    <div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticMenu"
        aria-labelledby="staticMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="staticMenuLabel">Conejo Blanco</h5>
            <i class="fi fi-br-cross icon-close" data-bs-dismiss="offcanvas" aria-label="Close"></i>
        </div>
        <form method="post" action="" class="offcanvas-body body-options-menu">
            <div class="mt-1"><a href="checkout.php" class="btn-option-menu"><i
                        class="fi fi-br-cash-register"></i><span>Corte de Caja</span></a></div>
            <div class="mt-1"><button type="submit" name="logout-session" class="btn-option-menu"><i
                        class="fi fi-br-power"></i><span>Cerrar Sesión</span></button></div>
        </form>
    </div>


    <div class="container-main-home" id="container-main-home">
        <!--FILTROS DE COMANDAS: botones fijos en el body, NO renderizados por React-->
        <div class="submenu-menu">
            <button type="button" class="option-submenu modulo-filtro" data-filtro="pendiente"
                disabled>Pendientes</button>
            <button type="button" class="option-submenu modulo-filtro" data-filtro="finalizado">Finalizados</button>
            <button type="button" class="option-submenu modulo-filtro" data-filtro="todos">Todas</button>
        </div>

        <div id="root"></div>
    </div>

    <!--MODAL ITEMS DE LA ORDEN-->
    <div class="modal fade" id="static-orderinfo" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="orderinfo-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="orderinfo-title"></span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" id="orderinfo-body"></div>
            </div>
        </div>
    </div>

    <!--MODAL PAGO-->
    <div class="modal fade fade-payments" id="static-payment" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="payment-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="payment-title"></span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" id="payment-body"></div>
            </div>
        </div>
    </div>

    <!--MODAL DESCUENTO-->
    <div class="modal fade fade-payments" id="static-discount" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="discount-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><span id="discount-title">Aplicar descuento</span></div>
                    <div><i class="fi fi-br-cross icon-close" data-bs-dismiss="modal" aria-label="Close"></i></div>
                </div>
                <div class="modal-body" id="discount-body"></div>
            </div>
        </div>
    </div>

    <!--ALERT-->
    <div class="fixed-top fullscreen total-center screen-alert" id="screen-alert">
        <div class="container-alert">
            <div class="container-title" id="title-alert">TITLE</div>
            <div class="container-message">
                <p class="text-center" id="message-alert">Message</p>
                <button class="btn-alert" onclick="hide_alert()">Aceptar</button>
            </div>
        </div>
    </div>

    <!--LOAD AFTER FORM-->
    <div class="fixed-top fullscreen total-center screen-load" id="screen-load">
        <div id="page">
            <div id="container-ring">
                <div id="ring"></div>
                <div id="ring"></div>
                <div id="ring"></div>
                <div id="ring"></div>
                <div id="h3">Cargando</div>
            </div>
        </div>
    </div>

    <!--LOAD MAIN-->
    <div class="fixed-top fullscreen total-center screen-preload" id="screen-preload">
        <div class="loader">
            <svg viewBox="0 0 80 80">
                <circle r="32" cy="40" cx="40" id="test"></circle>
            </svg>
        </div>

        <div class="loader triangle">
            <img src="<?= $root ?>files/rabbit-face.webp" class="img-fluid">
        </div>

        <div class="loader">
            <svg viewBox="0 0 80 80">
                <rect height="64" width="64" y="8" x="8"></rect>
            </svg>
        </div>
    </div>

</body>

</html>

<script src="<?= $root ?>script.js"></script>
<script src="script.js"></script>


<script type="text/babel">
    const { useState, useEffect, useRef, useCallback } = React;

    const API_URL = 'api-order.php';
    const ITEMS_API_URL = 'info-order.php';

    async function fetchPendingOrders() {
        const res = await fetch(API_URL);
        if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'Error al consultar órdenes');
        return json.data;
    }

    async function fetchOrderItems(orderId) {
        const res = await fetch(`${ITEMS_API_URL}?order=${orderId}`);
        if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'Error al consultar items');
        return json.data;
    }

    async function fetchOffers(orderId) {
        const res = await fetch(`get-offers.php?order=${orderId}`);
        if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'Error al consultar descuentos');
        return json.data;
    }

    const POLL_INTERVAL_MS = 5000;

    const ESTADO_CONFIG = {
        pendiente: { label: 'Pendiente', color: '#ffffff', bg: 'rgba(242,153,74,1)' },
        finalizado: { label: 'Finalizado', color: '#ffffff', bg: 'rgba(79,214,122,1)' },
        cancelado: { label: 'Cancelado', color: '#ffffff', bg: 'rgba(230,85,63,1)' },
    };

    function getEstadoConfig(estado) {
        const key = (estado || '').toLowerCase();
        return ESTADO_CONFIG[key] || ESTADO_CONFIG.pendiente;
    }

    function tiempoTranscurrido(ts) {
        if (!ts) return '';
        const diffMin = Math.floor((Date.now() - ts) / 60000);
        if (diffMin < 60) return `${diffMin} min`;
        const horas = Math.floor(diffMin / 60);
        const minutos = diffMin % 60;
        return `${horas}h ${minutos}min`;
    }

    function formatPrice(n) {
        return Number(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function computeBlockTotal(block) {
        const totalExtras = (block.extras || []).reduce(
            (sum, ex) => sum + Number(ex.price), 0
        );
        const unitTotal = Number(block.price) + totalExtras;
        const pendingQty = block.qty - (block.pagado || 0);
        return { unitTotal, lineTotal: unitTotal * block.qty, pendingQty };
    }

    function calcTip(base, tipType, tipValue) {
        if (tipType === 'none') return 0;
        if (tipType === 'percent') return Number(base) * (Number(tipValue || 0) / 100);
        return Number(tipValue || 0);
    }

    function OrderCard({ order, onSelect }) {
        const [elapsed, setElapsed] = useState(tiempoTranscurrido(order.creada_en));

        useEffect(() => {
            setElapsed(tiempoTranscurrido(order.creada_en));
            const id = setInterval(() => setElapsed(tiempoTranscurrido(order.creada_en)), 30000);
            return () => clearInterval(id);
        }, [order.creada_en]);

        const cfg = getEstadoConfig(order.estado);
        const estadoLower = (order.estado || '').toLowerCase();
        const mostrarContador = estadoLower !== 'finalizado' && estadoLower !== 'cancelado';
        const minutosEspera = Math.floor((Date.now() - order.creada_en) / 60000);
        const bordeDefault = estadoLower === 'finalizado' || estadoLower === 'cancelado';
        const esUrgente = minutosEspera >= 8 && estadoLower === 'pendiente';

        return (
            <div class="card-order" onClick={() => onSelect(order)}
                style={{ borderColor: bordeDefault ? '#000000' : (esUrgente ? 'var(--color-error)' : 'var(--color-success)') }}>
                <div class="card-top">
                    <div class="order-id">Orden #{order.id}</div>
                    <div>
                        <span class="badge" style={{ color: cfg.color, background: cfg.bg }}>{cfg.label}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="mesa">
                        {order.delivery == "mesa"
                            ? <span class="order-delivery-mesa">Mesa {order.mesa}</span>
                            : <span class="order-delivery-domicilio">Domicilio</span>}
                        <div>{order.client}</div>
                    </div>
                    <div class="d-flex gap-1">
                        {order.barra == 1 ? <div class="signal-process"><i class="fi fi-br-martini-glass-citrus"></i></div> : order.barra == 2 ? <div class="signal-process bg-success text-white"><i class="fi fi-br-martini-glass-citrus"></i></div> : ""}
                        {order.cocina == 1 ? <div class="signal-process"><i class="fi fi-br-restaurant"></i></div> : order.cocina == 2 ? <div class="signal-process bg-success text-white"><i class="fi fi-br-restaurant"></i></div> : ""}
                    </div>
                </div>
                <div class="card-bottom">
                    {mostrarContador && (
                        <div class={"d-flex align-items-center gap-1 elapsed" + (esUrgente ? " urgent" : "")}>
                            <i class="fi fi-br-clock-three"></i> {elapsed}
                        </div>
                    )}
                    {order.debt > 0 && order.total != order.debt
                        ? <div><span class="text-decoration-line-through">${(order.total ?? 0).toLocaleString('es-AR')}</span> <span class="total">${(order.debt ?? 0).toLocaleString('es-AR')}</span></div>
                        : <span class="total">${(order.total ?? 0).toLocaleString('es-AR')}</span>
                    }
                </div>
            </div>
        );
    }

    function TipInput({ base, tipType, tipValue, onTipTypeChange, onTipValueChange }) {
        const tipAmount = calcTip(base, tipType, tipValue);
        return (
            <div class="tip-input-group">
                <div class="container-propina">
                    <label class="form-label">Propina</label>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button"
                            class={"btn btn-outline-secondary" + (tipType === 'none' ? ' active' : '')}
                            onClick={() => onTipTypeChange('none')}>Sin propina</button>
                        <button type="button"
                            class={"btn btn-outline-secondary" + (tipType === 'percent' ? ' active' : '')}
                            onClick={() => onTipTypeChange('percent')}>%</button>
                        <button type="button"
                            class={"btn btn-outline-secondary" + (tipType === 'fixed' ? ' active' : '')}
                            onClick={() => onTipTypeChange('fixed')}>$</button>
                    </div>
                    <div class="container-detalles-propina">
                        <div>
                            {tipType !== 'none' && (
                                <input
                                    type="number"
                                    min="0"
                                    step={tipType === 'percent' ? '1' : '0.01'}
                                    value={tipValue}
                                    onChange={e => onTipValueChange(e.target.value)}
                                    required
                                />
                            )}
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <span class="reflect-propina">${formatPrice(tipAmount)}</span>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    function DiscountModal({ orderId, onApplied, onClose }) {
        const [loading, setLoading] = useState(true);
        const [offers, setOffers] = useState([]);
        const [error, setError] = useState(null);

        const [type, setType] = useState('porcentaje');
        const [value, setValue] = useState('');
        const [motivo, setMotivo] = useState('');
        const [submitting, setSubmitting] = useState(false);

        useEffect(() => {
            const el = document.getElementById('static-discount');
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();

            const handleHidden = () => onClose();
            el.addEventListener('hidden.bs.modal', handleHidden);

            return () => {
                el.removeEventListener('hidden.bs.modal', handleHidden);
                modal.hide();
            };
        }, []);

        const loadOffers = useCallback(() => {
            setLoading(true);
            fetchOffers(orderId)
                .then(data => { setOffers(data); setError(null); })
                .catch(e => setError(e.message))
                .finally(() => setLoading(false));
        }, [orderId]);

        useEffect(() => { loadOffers(); }, [loadOffers]);

        function handleDelete(offerId) {
            Swal.fire({
                title: '¿Desea eliminar el descuento?',
                icon: 'question',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('request', 'delete-offer');
                    formData.append('orden', orderId);
                    formData.append('offer', offerId);

                    show_load();

                    fetch('manage-offer.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(json => {
                            if (json.status === 201) {
                                show_alert(json.title, json.message);
                                loadOffers();
                                onApplied();
                            } else {
                                show_alert(json.title || 'Error', json.message || 'No se pudo eliminar el descuento');
                            }
                        })
                        .catch(() => {
                            show_alert('ERROR', 'Error al realizar operacion, Intente de nuevo');
                        })
                        .finally(() => {
                            hide_load();
                        });
                }
            });
        }

        function handleSubmit(e) {
            e.preventDefault();
            if (!value || Number(value) <= 0 || !motivo.trim()) return;

            const formData = new FormData();
            formData.append('request', 'order-offer');
            formData.append('order-to-offer', orderId);
            formData.append('type-offer', type);
            formData.append('value-offer', value);
            formData.append('motivo-offer', motivo);

            setSubmitting(true);
            show_load();

            fetch('manage-offer.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(json => {
                    if (json.status === 201) {
                        show_alert(json.title, json.message);
                        setValue('');
                        setMotivo('');
                        loadOffers();
                        onApplied();
                    } else {
                        show_alert(json.title || 'Error', json.message || 'No se pudo aplicar el descuento');
                    }
                })
                .catch(() => {
                    show_alert('ERROR', 'Error al realizar operacion, Intente de nuevo');
                })
                .finally(() => {
                    setSubmitting(false);
                    hide_load();
                });
        }

        const bodyEl = document.getElementById('discount-body');

        const body = loading ? (
            <div class="message-status">
                <div class="container-system-message">
                    <i class="fi fi-br-ballot"></i>
                    <span>Cargando descuentos...</span>
                </div>
            </div>
        ) : error ? (
            <div class="message-status">
                <div class="container-system-message">
                    <i class="fi fi-br-not-found"></i>
                    <span>{error}</span>
                </div>
            </div>
        ) : offers.length > 0 ? (
            <div class="d-grid gap-2">
                {offers.map(offer => (
                    <div key={offer.id} class="border border-2 text-general" style={{ fontSize: '0.85rem' }}>
                        <div class="d-flex align-items-center justify-content-between p-2 gap-2">
                            <span>
                                {offer.type === 'porcentaje'
                                    ? <React.Fragment><span class="text-primary fw-bold">{offer.value}%</span> (${formatPrice(offer.value_calculado)})</React.Fragment>
                                    : `$${formatPrice(offer.value)}`}
                            </span>
                            <i class="text-muted">{offer.date}</i>
                            <button class="bg-danger text-white" onClick={() => handleDelete(offer.id)}>
                                <i class="fi fi-br-trash"></i>
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        ) : (
            <div>
                <span>Detalles de los descuentos:</span>
                <li>Se calcula con base al total actual de la comanda.</li>

                <form onSubmit={handleSubmit}>
                    <div class="d-grid mt-2">
                        <label>Tipo de descuento</label>
                        <select value={type} onChange={e => setType(e.target.value)}>
                            <option value="porcentaje">Porcentaje</option>
                            <option value="fijo">Monto Fijo</option>
                        </select>

                        <label class="mt-2">Valor del descuento</label>
                        <input type="number" step="0.01" min="0" value={value} onChange={e => setValue(e.target.value)} required />

                        <label class="mt-2">Motivo de descuento</label>
                        <textarea value={motivo} onChange={e => setMotivo(e.target.value)} required></textarea>
                    </div>
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn-execute" disabled={submitting}>
                            {submitting ? 'Aplicando...' : 'Aplicar Descuento'}
                        </button>
                    </div>
                </form>
            </div>
        );

        return bodyEl ? ReactDOM.createPortal(body, bodyEl) : null;
    }

    function PaymentModal({ orderId, totalOrder, flatItems, initialMethod, onClose }) {
        const [step, setStep] = useState(initialMethod === 'separadas' ? 'select-items' : 'pay');
        const [method, setMethod] = useState(initialMethod === 'separadas' ? null : initialMethod);
        const [selectedQty, setSelectedQty] = useState({});
        const [tipType, setTipType] = useState('none');
        const [tipValue, setTipValue] = useState('0');
        const [receivedAmount, setReceivedAmount] = useState('');
        const [cashAmount, setCashAmount] = useState('');
        const [cashTipType, setCashTipType] = useState('none');
        const [cashTipValue, setCashTipValue] = useState('0');
        const [cardTipType, setCardTipType] = useState('none');
        const [cardTipValue, setCardTipValue] = useState('0');
        const [usarMontoParcial, setUsarMontoParcial] = useState(false);
        const [montoParcial, setMontoParcial] = useState('');

        useEffect(() => {
            const el = document.getElementById('static-payment');
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();

            const handleHidden = () => onClose();
            el.addEventListener('hidden.bs.modal', handleHidden);

            return () => {
                el.removeEventListener('hidden.bs.modal', handleHidden);
                modal.hide();
            };
        }, []);

        const bodyEl = document.getElementById('payment-body');
        const titleEl = document.getElementById('payment-title');

        function setItemQty(id, qty, maxQty) {
            const clamped = Math.min(Math.max(qty, 0), maxQty);
            setSelectedQty(prev => ({ ...prev, [id]: clamped }));
        }

        const subtotalSeleccionado = flatItems.reduce(
            (sum, it) => sum + (selectedQty[it.id] || 0) * it.unitTotal, 0
        );
        const hayAlgoSeleccionado = Object.values(selectedQty).some(q => q > 0);
        const baseAmount = initialMethod === 'separadas' ? subtotalSeleccionado : totalOrder;

        async function confirmarPago(payload) {
            const body = {
                order_id: orderId,
                method: payload.method,       // 'efectivo' | 'tarjeta' | 'transferencia' | 'mixto'
                base: payload.base,         // monto base sin propina
                tip: payload.tip ?? 0,     // propina total
                total: payload.total,        // lo que se cobra (base + tip)
                received: payload.received ?? null,  // solo efectivo: con cuánto pagó
                change: payload.change ?? null,   // solo efectivo: cambio
                // solo mixto:
                details: payload.details ?? null,
                // {cash, cashTip, card, cardTip}
                // solo cuentas separadas:
                item_ids: payload.itemIds ?? null,
                // [{id, qty}] — items y cantidades que cubre este pago
                // solo tarjeta/transferencia con monto parcial:
                partial: payload.partial ?? null,
                // monto real cobrado si es menor al total
                debt: payload.debt ?? null,
            };

            try {
                const res = await fetch('crud-payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const json = await res.json();
                if (json.status === 201) {
                    const mensaje = json.debt > 0
                        ? `Falta por pagar: $${formatPrice(json.debt)}`
                        : `Cuenta saldada`;
                    show_alert('Pago registrado', mensaje);
                    window.dispatchEvent(new CustomEvent('pago-registrado', { detail: { orderId } }));
                    bootstrap.Modal.getInstance(document.getElementById('static-payment')).hide();
                } else {
                    show_alert(json.title || 'Error', json.message || 'No se pudo registrar el pago');
                }
            } catch (e) {
                show_alert('Error', 'No se pudo conectar con el servidor');
            }
        }

        let title = 'Cobrar orden';
        let body = null;

        if (step === 'select-items') {
            title = 'Cuentas Separadas — Selecciona los Productos';
            body = (
                <div>
                    <div class="list-products-separated">
                        {flatItems.map(it => {
                            const qty = selectedQty[it.id] || 0;
                            return (
                                <div key={it.id} class="list-group-product">
                                    <div>
                                        <div>{it.name}</div>
                                        <small class="text-muted">
                                            ${formatPrice(it.unitTotal)} c/u — {it.pendingQty} pendiente{it.pendingQty > 1 ? 's' : ''}
                                        </small>
                                    </div>
                                    <div class="container-buttons-number">
                                        <button type="button" onClick={() => setItemQty(it.id, qty - 1, it.pendingQty)}>
                                            <i class="fi fi-br-minus-small"></i>
                                        </button>
                                        <span class="number">{qty}</span>
                                        <button type="button" onClick={() => setItemQty(it.id, qty + 1, it.pendingQty)}>
                                            <i class="fi fi-br-plus-small"></i>
                                        </button>
                                    </div>
                                    <div class="number" align="end">
                                        <span>${formatPrice(qty * it.unitTotal)}</span>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <div class="subtotal-selected">
                        <span>Subtotal seleccionado</span>
                        <b>${formatPrice(subtotalSeleccionado)}</b>
                    </div>
                    <div class="d-grid">
                        <button
                            class="btn-execute"
                            disabled={!hayAlgoSeleccionado}
                            onClick={() => setStep('choose-method')}>
                            Continuar
                        </button>
                    </div>
                </div>
            );
        } else if (step === 'choose-method') {
            title = 'Cuentas Separadas — Método de Pago';
            body = (
                <div class="d-grid gap-2">
                    <div class="subtotal-selected">
                        <span>Subtotal a cobrar</span>
                        <b>${formatPrice(baseAmount)}</b>
                    </div>
                    <button class="btn btn-dark" onClick={() => { setMethod('efectivo'); setStep('pay'); }}>Efectivo</button>
                    <button class="btn btn-dark" onClick={() => { setMethod('tarjeta'); setStep('pay'); }}>Tarjeta</button>
                    <button class="btn btn-dark" onClick={() => { setMethod('transferencia'); setStep('pay'); }}>Transferencia</button>
                    <button class="btn btn-dark" onClick={() => { setMethod('mixto'); setStep('pay'); }}>Mixto</button>
                    <button class="btn btn-secondary" onClick={() => setStep('select-items')}>Volver a elegir productos</button>
                </div>
            );
        } else if (method === 'mixto') {
            title = 'Pago mixto';
            const cash = Number(cashAmount || 0);
            const card = Math.max(baseAmount - cash, 0);
            const cashTip = calcTip(cash, cashTipType, cashTipValue);
            const cardTip = calcTip(card, cardTipType, cardTipValue);
            const totalACobrar = cash + card + cashTip + cardTip;

            body = (
                <div class="subbody-detalles-cuenta">
                    <div class="container-detalles-cuenta">
                        <b>Cuenta</b>
                        <span>${formatPrice(baseAmount)}</span>
                    </div>
                    <div class="container-input-recibido">
                        <label class="form-label">Monto en efectivo</label>
                        <input type="number" min="0" max={baseAmount} step="0.01"
                            value={cashAmount} onChange={e => setCashAmount(e.target.value)} />
                    </div>
                    <TipInput base={cash} tipType={cashTipType} tipValue={cashTipValue}
                        onTipTypeChange={setCashTipType} onTipValueChange={setCashTipValue} />
                    <div class="container-restante">
                        <div><label class="form-label">Resto en tarjeta</label></div>
                        <div><span class="amount">${formatPrice(card)}</span></div>
                    </div>
                    <TipInput base={card} tipType={cardTipType} tipValue={cardTipValue}
                        onTipTypeChange={setCardTipType} onTipValueChange={setCardTipValue} />
                    <div class="container-amounts">
                        <div class="amount-detail">
                            <span>Efectivo + propina</span>
                            <span class="amount">${formatPrice(cash + cashTip)}</span>
                        </div>
                        <div class="amount-detail">
                            <span>Tarjeta + propina</span>
                            <span class="amount">${formatPrice(card + cardTip)}</span>
                        </div>
                        <div class="amount-detail-main">
                            <b>Total a cobrar</b>
                            <span class="amount">${formatPrice(totalACobrar)}</span>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn-execute" onClick={() => confirmarPago({
                            method: 'mixto', base: baseAmount,
                            details: { cash, cashTip, card, cardTip },
                            total: totalACobrar,
                            itemIds: initialMethod === 'separadas'
                                ? Object.entries(selectedQty).filter(([, q]) => q > 0).map(([id, qty]) => ({ id: Number(id), qty }))
                                : null
                        })}>Confirmar pago</button>
                    </div>
                </div>
            );
        } else {
            const labels = { efectivo: 'Pago en efectivo', tarjeta: 'Pago con tarjeta', transferencia: 'Pago por transferencia' };
            title = labels[method] || 'Cobrar';

            const montoReal = usarMontoParcial ? Number(montoParcial || 0) : baseAmount;

            // Propina SIEMPRE sobre baseAmount
            const tip = calcTip(baseAmount, tipType, tipValue);

            const propinaSinValor = tipType !== 'none' && (tipValue === '' || tipValue === null);
            const totalACobrar = Number(montoReal) + tip;
            const received = Number(receivedAmount || 0);
            const cambio = received - totalACobrar;

            // Abono y deuda para tarjeta/transferencia con monto parcial
            const abonoACuenta = montoReal;                       // lo que se puso en "pagar otra cantidad"
            const deuda = Math.max(baseAmount - montoReal, 0);     // cuenta sin propina - lo recibido

            // Falta en efectivo cuando paga menos
            const falta = received > 0 && cambio < 0 ? Math.abs(cambio) : 0;

            const montoParcialInvalido = usarMontoParcial && (
                !montoParcial || Number(montoParcial) <= 0 || Number(montoParcial) > baseAmount
            );

            // Una sola declaración de puedeConfirmar
            const puedeConfirmar =
                !propinaSinValor &&
                !montoParcialInvalido &&
                !(method === 'efectivo' && receivedAmount === '');

            body = (
                <div class="subbody-detalles-cuenta">
                    <div class="container-detalles-cuenta">
                        <b>Cuenta</b>
                        <span>${formatPrice(baseAmount)}</span>
                    </div>

                    {(method === 'tarjeta' || method === 'transferencia') && (
                        <div class="container-input-abono">
                            <div>
                                <input
                                    type="checkbox"
                                    className="btn-check"
                                    id="btn-check-outlined"
                                    autoComplete="off"
                                    checked={usarMontoParcial}
                                    onChange={(e) => {
                                        setUsarMontoParcial(e.target.checked);
                                        setMontoParcial('');
                                    }}
                                />

                                <label
                                    className="btn btn-outline-dark"
                                    htmlFor="btn-check-outlined"
                                >
                                    Abonar
                                </label>
                            </div>
                            {usarMontoParcial && (
                                <>
                                    <input
                                        type="number" min="0.01" max={baseAmount} step="0.01"
                                        placeholder={`Máximo $${formatPrice(baseAmount)}`}
                                        value={montoParcial}
                                        onChange={e => setMontoParcial(e.target.value)}
                                        required
                                    />
                                </>
                            )}
                        </div>
                    )}

                    <TipInput
                        base={baseAmount}
                        tipType={tipType}
                        tipValue={tipValue}
                        onTipTypeChange={t => { setTipType(t); setTipValue(''); }}
                        onTipValueChange={setTipValue}
                    />

                    <div>
                        {!montoParcialInvalido && montoParcial !== '' && (
                            <>
                                <div class="container-detalles-calculos">
                                    <span>Abono a cuenta</span>
                                    <b>${formatPrice(abonoACuenta)}</b>
                                </div>
                                <div class="container-detalles-calculos">
                                    <span>Queda a deber</span>
                                    <b>${formatPrice(deuda)}</b>
                                </div>
                            </>
                        )}
                        <div class="container-total-final">
                            <b>{method === 'efectivo' ? 'Total' : 'Recibido'}</b>
                            <span>${formatPrice(totalACobrar)}</span>
                        </div>
                    </div>


                    {method === 'efectivo' && (
                        <React.Fragment>
                            <div class="container-input-recibido">
                                <label class="form-label">Recibido</label>
                                <input
                                    type="number" min="0" step="0.01"
                                    value={receivedAmount}
                                    onChange={e => setReceivedAmount(e.target.value)}
                                    required
                                />
                            </div>
                            {receivedAmount !== '' && (
                                cambio >= 0 ? (
                                    <div class="container-cambio">
                                        <span>Cambio:</span>
                                        <b class="amount text-success">${formatPrice(cambio)}</b>
                                    </div>
                                ) : (
                                    <div class="container-cambio">
                                        <span>Queda a deber:</span>
                                        <b class="amount text-danger">${formatPrice(Math.abs(cambio))}</b>
                                    </div>
                                )
                            )}
                        </React.Fragment>
                    )}

                    <div class="d-grid">
                        <button
                            class="btn-execute"
                            disabled={!puedeConfirmar}
                            onClick={() => confirmarPago({
                                method,
                                base: baseAmount,
                                tip,
                                total: totalACobrar,
                                partial: usarMontoParcial ? montoReal : null,
                                debt: usarMontoParcial ? deuda : (falta > 0 ? falta : null),
                                received: method === 'efectivo' ? received : null,
                                change: method === 'efectivo' && cambio >= 0 ? cambio : null,
                                itemIds: initialMethod === 'separadas'
                                    ? Object.entries(selectedQty).filter(([, q]) => q > 0).map(([id, qty]) => ({ id: Number(id), qty }))
                                    : null
                            })}>
                            Confirmar pago
                        </button>
                    </div>
                </div>
            );

            console.log({ baseAmount, tipType, tipValue, tip, montoReal, totalACobrar });
        }


        return (
            <React.Fragment>
                {titleEl && ReactDOM.createPortal(title, titleEl)}
                {bodyEl && ReactDOM.createPortal(body, bodyEl)}
            </React.Fragment>
        );
    }

    function ItemsModal({ orderId, order, onClose }) {
        const [batches, setBatches] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState(null);
        const [paymentMethod, setPaymentMethod] = useState(null);
        const [showDiscountModal, setShowDiscountModal] = useState(false);

        useEffect(() => {
            const el = document.getElementById('static-orderinfo');
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();

            const handleHidden = () => onClose();
            el.addEventListener('hidden.bs.modal', handleHidden);

            return () => {
                el.removeEventListener('hidden.bs.modal', handleHidden);
                modal.hide();
            };
        }, []);

        const loadItems = useCallback(() => {
            let cancelado = false;
            setLoading(true);
            fetchOrderItems(orderId)
                .then(data => { if (!cancelado) { setBatches(data); setError(null); } })
                .catch(e => { if (!cancelado) setError(e.message); })
                .finally(() => { if (!cancelado) setLoading(false); });
            return () => { cancelado = true; };
        }, [orderId]);

        useEffect(() => loadItems(), [loadItems]);

        useEffect(() => {
            function handlePago(e) {
                if (e.detail?.orderId === orderId) loadItems();
            }
            window.addEventListener('pago-registrado', handlePago);
            return () => window.removeEventListener('pago-registrado', handlePago);
        }, [orderId, loadItems]);

        const bodyEl = document.getElementById('orderinfo-body');
        const titleEl = document.getElementById('orderinfo-title');

        // Todos los items para el modal (incluyendo pagados)
        const allItems = batches.flatMap(batch => batch.items.map(block => {
            const { unitTotal, lineTotal, pendingQty } = computeBlockTotal(block);
            return { id: block.id, name: block.name, qty: block.qty, pagado: block.pagado || 0, pendingQty, unitTotal, lineTotal };
        }));

        // Solo pendientes para cuentas separadas
        const flatItems = allItems.filter(it => it.pendingQty > 0);

        // Total bruto de toda la orden
        const totalOrderBruto = order.total;

        // Total pendiente de pago
        const totalPendiente = order.debt;

        const hayPagados = order.paid > 0;
        const faltaPagar = order.debt > 0;

        const content = loading ? (
            <div class="message-status">
                <div class="container-system-message">
                    <i class="fi fi-br-ballot"></i>
                    <span>Cargando Items...</span>
                </div>
            </div>
        ) : error ? (
            <div class="message-status">
                <div class="container-system-message">
                    <i class="fi fi-br-not-found"></i>
                    <span>{error}</span>
                </div>
            </div>
        ) : batches.length === 0 ? (
            <div class="message-status">
                <div class="container-system-message">
                    <i class="fi fi-br-hamburger-soda"></i>
                    <span>Sin items</span>
                </div>
            </div>
        ) : (
            <div class="modal-items">
                <div className="p-2 border border-2 rounded d-flex align-items-center text-center"><i class="fi fi-br-holding-hand-dinner fs-4 me-1"></i> {order.mesero}</div>
                {batches.map(batch => (
                    <div key={batch.batch_id} class="batch-block">
                        <div class="batch-header">
                            <span class="batch-number">{batch.batch_seq == 1 ? 'Creado' : 'Agregado después'}</span>
                            <div class="batch-fecha">
                                <span class="me-2">{new Date(batch.batch_created_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit' })}</span>
                                <span>{new Date(batch.batch_created_at).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                        </div>

                        {batch.items.map(block => {
                            const { unitTotal, lineTotal } = computeBlockTotal(block);
                            const pagado = block.pagado || 0;
                            const debe = unitTotal * (block.qty - pagado);

                            return (
                                <div key={block.id} class="item-block">
                                    <div class="subcontainer-item-block">
                                        <div class="d-grid align-items-center gap-2">
                                            <div class="item-main">
                                                <span>
                                                    {block.type == 'combo' ? <i class="fi fi-br-hamburger-soda signal-combo"></i> : ''}
                                                    {block.type == 'especial' ? <i class="fi fi-br-crown signal-crown"></i> : ''}
                                                    {block.name}
                                                </span>
                                                <span class="price-unit">(${formatPrice(block.price)})</span>
                                            </div>

                                            {block.type === 'combo' && block.groups && block.groups.map(g => (
                                                <div key={g.group_id} class="combo-group">
                                                    <div class="combo-group-name">{g.group_name}</div>
                                                    <div class="item-products-selected">
                                                        {g.items.map((it, i) => (
                                                            <div key={i}>
                                                                <span>
                                                                    <b class="text-danger">{`${it.qty} x`}</b> {it.name}
                                                                    {it.is_extra ? <span class="fz-tag text-white bg-primary rounded ms-1 p-0 ps-2 pe-2">Extra</span> : ''}
                                                                </span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="d-flex align-items-center justify-content-start flex-column gap-1">
                                            <div className={`qty-item ${pagado === block.qty && pagado > 0 ? 'bg-success text-white' : ''}`}>
                                                {block.qty}
                                            </div>

                                            {pagado > 0 && pagado < block.qty && (
                                                <div className="pagado-indicator">
                                                    <div className="badge border border-warning border-2 text-dark">
                                                        <b>{pagado}</b> <span class="fw-light text-capitalize">{pagado > 1 ? 'Pagados' : 'Pagado'}</span>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {block.extras && block.extras.length > 0 && (
                                        <div class="item-extras">
                                            {block.extras.map(ex => (
                                                <div key={ex.id}>
                                                    <span>+ <b class="text-danger">{`${ex.qty} x`}</b> {ex.name}</span>
                                                    <span class="price-unit">(${formatPrice(ex.price)})</span>
                                                    {ex.note && <div class="item-note">{ex.note}</div>}
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {block.note && <div class="subcontainer-comments">{block.note}</div>}

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="price-unit m-0">${formatPrice(unitTotal)}</span>
                                        <div className="pagado-indicator">
                                            <span className="price-unit m-0">
                                                <span className={`${pagado > 0 && pagado < block.qty ? 'fw-light text-decoration-line-through' : ' fw-bold'}`}>${formatPrice(lineTotal)}</span>

                                                {pagado > 0 && pagado < block.qty && (
                                                    <>
                                                        {" "}
                                                        <b class="fw-bold"> ${formatPrice(debe)}</b>
                                                    </>
                                                )}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ))
                }

                <div class="container-amount-total">
                    <b>Total</b>
                    <span>${formatPrice(order.total)}</span>
                </div>

                {
                    order.offer > 0 && (
                        <div class="container-amount-total">
                            <b>Descuento</b>
                            <span>-${formatPrice(order.discount)}</span>
                        </div>
                    )
                }

                {
                    hayPagados && (
                        <div class="container-amount-total">
                            <b>Pagado</b>
                            <span>${formatPrice(order.paid)}</span>
                        </div>
                    )
                }

                {
                    faltaPagar && (
                        <div class="container-amount-total">
                            <b>Pendiente</b>
                            <span>${formatPrice(order.debt)}</span>
                        </div>
                    )
                }

                {order.estado === 'finalizado' ? (
                    <div className="p-2 border border-success border-2 rounded">
                        <div className="text-success fw-boldr">Orden Finalizada</div>
                        <p className="p-0 m-0" align="justify">La orden fue pagada en su totalidad.</p>
                    </div>
                ) : order.estado === 'cancelado' ? (
                    <div className="p-2 border border-danger border-2 rounded">
                        <div className="text-danger fw-boldr">Orden Cancelada</div>
                        <p className="p-0 m-0" align="justify">{order.notes}</p>
                    </div>
                ) : null}

                <div className="container-pay-options">
                    <button className="btn btn-dark" onClick={generarRecibo}>Recibo</button>
                    {order.estado === 'pendiente' ? (
                        order.barra !== 1 && order.cocina !== 1 ? (
                            <>
                                <button
                                    className="btn btn-dark"
                                    onClick={() => setShowDiscountModal(true)}
                                >
                                    Descuento
                                </button>

                                <button
                                    className="btn btn-success"
                                    onClick={() => setPaymentMethod('efectivo')}
                                >
                                    Pago en Efectivo
                                </button>

                                <button
                                    className="btn btn-danger"
                                    onClick={() => setPaymentMethod('tarjeta')}
                                >
                                    Pago con Tarjeta
                                </button>

                                <button
                                    className="btn btn-primary"
                                    onClick={() => setPaymentMethod('transferencia')}
                                >
                                    Pago con Transferencia
                                </button>

                                <button
                                    className="btn btn-info"
                                    onClick={() => setPaymentMethod('mixto')}
                                >
                                    Pago Mixto
                                </button>

                                {order.deposito == 0 && order.offer == 0 && (
                                    <button
                                        className="btn btn-warning"
                                        onClick={() => setPaymentMethod('separadas')}
                                    >
                                        Cuentas Separadas
                                    </button>
                                )}
                            </>
                        ) : (
                            <div className="p-2 text-center">Preparando...</div>
                        )
                    ) : null}
                </div>
            </div>
        );


        function generarRecibo() {
            const fecha = new Date().toLocaleString('es-MX', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });

            let filas = '';
            batches.forEach(batch => {
                batch.items.forEach(block => {
                    const { unitTotal, lineTotal } = computeBlockTotal(block);

                    filas += `
                <div class="linea-item">
                    <div class="linea-principal">
                        <span>${block.qty} x ${block.name}</span>
                        <span>$${formatPrice(lineTotal)}</span>
                    </div>
                    <div class="linea-precio-unit">($${formatPrice(unitTotal)} c/u)</div>
            `;

                    if (block.type === 'combo' && block.groups) {
                        block.groups.forEach(g => {
                            g.items.forEach(it => {
                                filas += `<div class="linea-detalle">&nbsp;&nbsp;- ${it.qty} x ${it.name}${it.is_extra ? ' (Extra)' : ''}</div>`;
                            });
                        });
                    }

                    if (block.extras && block.extras.length > 0) {
                        block.extras.forEach(ex => {
                            filas += `<div class="linea-detalle">&nbsp;&nbsp;+ ${ex.qty} x ${ex.name} ($${formatPrice(ex.price)})</div>`;
                        });
                    }

                    if (block.note) {
                        filas += `<div class="linea-nota">"${block.note}"</div>`;
                    }

                    filas += `</div>`;
                });
            });

            const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Recibo #${orderId}</title>
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: 'Courier New', monospace;
                    width: 280px;
                    margin: 0 auto;
                    padding: 10px;
                    font-size: 12px;
                    color: #000;
                }
                .centrado { text-align: center; }
                .separador { border-top: 1px dashed #000; margin: 8px 0; }
                .fila-total {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 2px;
                }
                .linea-item { margin-bottom: 6px; }
                .linea-principal {
                    display: flex;
                    justify-content: space-between;
                    font-weight: bold;
                }
                .linea-precio-unit { font-size: 10px; color: #333; }
                .linea-detalle { font-size: 11px; margin-left: 4px; }
                .linea-nota { font-size: 10px; font-style: italic; margin-left: 4px; }
                h2 { margin: 4px 0; }
                p { margin: 2px 0; }
                @media print {
                    body { width: 100%; }
                }
            </style>
        </head>
        <body>
            <div class="centrado">
                <h2>Comanda #${orderId}</h2>
                <p>${order.delivery === 'mesa' ? 'Mesa ' + order.mesa : 'Domicilio'}</p>
                <p>${order.client || ''}</p>
                <p>Atendió: ${order.mesero || ''}</p>
                <p>${fecha}</p>
            </div>

            <div class="separador"></div>

            ${filas}

            <div class="separador"></div>

            <div class="fila-total">
                <span>Total</span>
                <span>$${formatPrice(order.total)}</span>
            </div>
            ${order.offer > 0 ? `
                <div class="fila-total">
                    <span>Descuento</span>
                    <span>-$${formatPrice(order.discount)}</span>
                </div>
            ` : ''}
            ${order.paid > 0 ? `
                <div class="fila-total">
                    <span>Pagado</span>
                    <span>$${formatPrice(order.paid)}</span>
                </div>
            ` : ''}
            ${order.debt > 0 ? `
                <div class="fila-total">
                    <span>Pendiente</span>
                    <span>$${formatPrice(order.debt)}</span>
                </div>
            ` : ''}

            <div class="separador"></div>
            <p class="centrado">¡Gracias por su preferencia!</p>
        </body>
        </html>
    `;

            const ventana = window.open('', '_blank', 'width=320,height=600');
            ventana.document.write(html);
            ventana.document.close();
            ventana.focus();
            ventana.onload = () => {
                ventana.print();
            };
        }

        return (
            <React.Fragment>
                {titleEl && ReactDOM.createPortal(`Comanda #${orderId}`, titleEl)}
                {bodyEl && ReactDOM.createPortal(content, bodyEl)}
                {paymentMethod && (
                    <PaymentModal
                        orderId={orderId}
                        totalOrder={order.debt}
                        flatItems={flatItems}
                        initialMethod={paymentMethod}
                        onClose={() => setPaymentMethod(null)}
                    />
                )}
                {showDiscountModal && (
                    <DiscountModal
                        orderId={orderId}
                        onApplied={() => window.dispatchEvent(new CustomEvent('pago-registrado', { detail: { orderId } }))}
                        onClose={() => setShowDiscountModal(false)}
                    />
                )}
            </React.Fragment>
        );
    }

    function App() {
        const [orders, setOrders] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState(null);
        const [filtro, setFiltro] = useState('pendiente');
        const [selectedOrder, setSelectedOrder] = useState(null);
        const intervalRef = useRef(null);

        const loadOrders = useCallback(async () => {
            try {
                const data = await fetchPendingOrders();
                setOrders(data);
                setError(null);
            } catch (e) {
                setError(e.message || 'Error al consultar órdenes');
            } finally {
                setLoading(false);
            }
        }, []);

        useEffect(() => { loadOrders(); }, [loadOrders]);

        useEffect(() => {
            intervalRef.current = setInterval(loadOrders, POLL_INTERVAL_MS);
            return () => clearInterval(intervalRef.current);
        }, [loadOrders]);

        useEffect(() => {
            function handleFiltro(e) { setFiltro(e.detail); loadOrders(); }
            window.addEventListener('filtro-comandas', handleFiltro);
            return () => window.removeEventListener('filtro-comandas', handleFiltro);
        }, [loadOrders]);

        useEffect(() => {
            async function handlePago(e) {
                await loadOrders();
                // Después de recargar, actualiza el selectedOrder con los datos frescos
                setOrders(prev => {
                    const ordenActualizada = prev.find(o => o.id === e.detail?.orderId);
                    if (ordenActualizada) setSelectedOrder(ordenActualizada);
                    return prev;
                });
            }
            window.addEventListener('pago-registrado', handlePago);
            return () => window.removeEventListener('pago-registrado', handlePago);
        }, [loadOrders]);

        const filteredOrders = orders
            .filter(o => {
                const estado = (o.estado || '').toLowerCase();
                if (filtro === 'todos') return true;
                return estado === filtro;
            })
            .slice()
            .sort((a, b) => {
                if (filtro === 'finalizado') return (b.finalizada_en ?? 0) - (a.finalizada_en ?? 0);
                if (filtro === 'todos') return (a.id ?? 0) - (b.id ?? 0);
                return 0;
            });

        return (
            <div class="app">
                <div class="title-comandas">
                    <div><span class="title"></span></div>
                    <div class="status-line">
                        {error && <span class="error-text">{error} - Reintentando...</span>}
                    </div>
                </div>

                {loading ? (
                    <div class="message-status" key="loading">
                        <div class="container-system-message">
                            <i class="fi fi-br-ballot"></i>
                            <span>Cargando lista</span>
                        </div>
                    </div>
                ) : filteredOrders.length === 0 ? (
                    <div class="message-status" key={filtro}>
                        <div class="container-system-message">
                            <i class="fi fi-br-order-food-online"></i>
                            <span>Sin comandas en la lista</span>
                        </div>
                    </div>
                ) : (
                    <div class="list-orders" key={filtro}>
                        {filteredOrders.map(o => <OrderCard key={o.id} order={o} onSelect={setSelectedOrder} />)}
                    </div>
                )}

                {selectedOrder && (
                    <ItemsModal orderId={selectedOrder.id} order={selectedOrder} onClose={() => setSelectedOrder(null)} />
                )}
            </div>
        );
    }

    ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>