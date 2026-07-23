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
    <title><?= $app_name ?> - Mesero</title>
    <link href="<?= $root ?>style.css" rel="stylesheet">
    <link href="<?= $root ?>style-loader.css" rel="stylesheet">
    <link href="<?= $root ?>style-alert.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body id="tag-body">

    <!--DISEÑO INDEX (CONTENEDOR PRINCIPAL)-->
    <div class="fixed-top system-navbar">
        <div class="d-flex align-items-center">
            <a href="home.php"><img src="<?= $root ?>files/rabbit-mesero.png" class="navbar-logo"></a>
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
            <div class="mt-1"><a href="mesero-new-comanda.php" class="btn-option-menu"><i
                        class="fi fi-br-hamburger-soda"></i><span>Nueva Comanda</span></a></div>
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

    /* ============================================================
       API REAL — apunta al endpoint PHP (api-order.php)
       ============================================================ */

    const API_URL = 'api-order.php';
    const ITEMS_API_URL = 'info-order.php';

    async function fetchPendingOrders() {
        const res = await fetch(API_URL);
        if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'Error al consultar órdenes');
        return json.data; // [{ id, mesa, estado, creada_en, total, notes }, ...]
    }

    async function fetchOrderItems(orderId) {
        const res = await fetch(`${ITEMS_API_URL}?order=${orderId}`);
        if (!res.ok) throw new Error(`Error de conexión ${res.status}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'Error al consultar items');
        return json.data;
        // [{ batch_id, batch_seq, batch_created_at, items: [
        //     { id, name, qty, note, type: 'producto', extras: [{id,name,qty,note}] },
        //     { id, name, qty, note, type: 'combo', extras: [...], groups: [{group_id, group_name, group_type, items: [{name, qty, is_extra, note}]}] }
        // ] }, ...]
    }
    /* ============================================================
       FIN API REAL
       ============================================================ */

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

    // Cambio 1: solo minutos si es menos de 1h, si no horas + minutos (sin segundos)
    function tiempoTranscurrido(ts) {
        if (!ts) return '';
        const diffMin = Math.floor((Date.now() - ts) / 60000);
        if (diffMin < 60) return `${diffMin} min`;
        const horas = Math.floor(diffMin / 60);
        const minutos = diffMin % 60;
        return `${horas}h ${minutos}min`;
    }

    // Helper: formatea un número como precio $X,XXX.XX
    function formatPrice(n) {
        return Number(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Helper: precio unitario del item (base + extras) y total de línea (unitario x qty)
    function computeBlockTotal(block) {
        const totalExtras = (block.extras || []).reduce(
            (sum, ex) => sum + Number(ex.price) * ex.qty,
            0
        );
        const unitTotal = Number(block.price) + totalExtras;
        return { unitTotal, lineTotal: unitTotal * block.qty };
    }

    // Helper: propina según tipo ('percent' | 'fixed' | 'none')
    function calcTip(base, tipType, tipValue) {
        if (tipType === 'none') return 0;
        if (tipType === 'percent') return Number(base) * (Number(tipValue || 0) / 100);
        return Number(tipValue || 0);
    }

    function OrderCard({ order, onSelect }) {
        const [elapsed, setElapsed] = useState(tiempoTranscurrido(order.creada_en));

        useEffect(() => {
            setElapsed(tiempoTranscurrido(order.creada_en));
            // Se actualiza cada 30s: alcanza de sobra ya que ahora se muestra en minutos/horas
            const id = setInterval(() => setElapsed(tiempoTranscurrido(order.creada_en)), 30000);
            return () => clearInterval(id);
        }, [order.creada_en]);

        const cfg = getEstadoConfig(order.estado);
        const estadoLower = (order.estado || '').toLowerCase();
        const mostrarContador = estadoLower !== 'finalizado' && estadoLower !== 'cancelado';
        const minutosEspera = Math.floor((Date.now() - order.creada_en) / 60000);
        const esUrgente = minutosEspera >= 8 && estadoLower === 'pendiente';

        return (
            <div class="card-order" onClick={() => onSelect(order.id)}
                style={{ borderColor: esUrgente ? 'var(--color-error)' : 'var(--color-success)' }}>
                <div class="card-top">
                    <div class="order-id">
                        Orden #{order.id}
                    </div>
                    <div>
                        <span class="badge" style={{ color: cfg.color, background: cfg.bg }}>{cfg.label}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="mesa">
                        {order.delivery == "mesa" ? <span class="order-delivery-mesa">Mesa {order.mesa}</span> : <span class="order-delivery-domicilio">Domicilio</span>}
                        <div>{order.client}</div>
                    </div>
                    <div class="d-flex gap-1">
                        {order.barra == 1 ? <div class="signal-process"><i class="fi fi-br-martini-glass-citrus"></i></div> : ""}
                        {order.cocina == 1 ? <div class="signal-process"><i class="fi fi-br-restaurant"></i></div> : ""}
                    </div>
                </div>
                <div class="card-bottom">
                    {mostrarContador && (
                        <div class={"d-flex align-items-center gap-1 elapsed" + (esUrgente ? " urgent" : "")}><i class="fi fi-br-clock-three"></i> {elapsed}</div>
                    )}
                    <span class="total">${(order.total ?? 0).toLocaleString('es-AR')}</span>
                </div>
            </div>
        );
    }

    /**
     * TipInput: selector de propina reutilizable (sin propina, porcentaje
     * o monto fijo) más el monto ya calculado, para usar en cualquier
     * método de pago.
     */
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

    /**
     * DiscountModal: modal aparte (#static-discount) para ingresar un
     * monto de descuento sobre el total de la orden. No se descuenta
     * de nada más (ni items ni propinas), es un monto plano.
     */
    function DiscountModal({ currentDiscount, maxAmount, onConfirm, onClose }) {
        const [value, setValue] = useState(currentDiscount > 0 ? String(currentDiscount) : '');

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

        const bodyEl = document.getElementById('discount-body');

        // El descuento nunca puede ser negativo ni mayor al total de la orden
        const monto = Math.min(Math.max(Number(value || 0), 0), maxAmount);

        const body = (
            <div>
                <div class="container-detalles-cuenta">
                    <b>Total de la orden</b>
                    <span>${formatPrice(maxAmount)}</span>
                </div>

                <div class="container-input-recibido">
                    <label class="form-label">Monto a descontar</label>
                    <input
                        type="number"
                        min="0"
                        max={maxAmount}
                        step="0.01"
                        value={value}
                        onChange={e => setValue(e.target.value)}
                    />
                    <small class="text-muted d-block mt-1">Máximo: ${formatPrice(maxAmount)}</small>
                </div>

                <div class="container-total-final">
                    <b>Total con descuento</b>
                    <span>${formatPrice(maxAmount - monto)}</span>
                </div>

                <div class="d-grid mt-2">
                    <button class="btn-execute" onClick={() => {
                        onConfirm(monto);
                        bootstrap.Modal.getInstance(document.getElementById('static-discount')).hide();
                    }}>
                        Aplicar descuento
                    </button>
                </div>
            </div>
        );

        return bodyEl ? ReactDOM.createPortal(body, bodyEl) : null;
    }

    /**
     * PaymentModal: maneja los 5 métodos de cobro (efectivo, tarjeta,
     * transferencia, mixto, cuentas separadas) sobre un modal de Bootstrap
     * compartido (#static-payment).
     *
     * - efectivo: monto base + propina + "¿con cuánto paga?" -> cambio.
     * - tarjeta / transferencia: monto base + 1 propina.
     * - mixto: se reparte el monto entre efectivo y tarjeta (el resto),
     *   cada uno con su propia propina.
     * - separadas: primer paso elegís qué productos entran en esta cuenta
     *   (checklist sobre flatItems), después elegís uno de los 4 métodos
     *   de arriba pero aplicado solo al subtotal seleccionado.
     */
    function PaymentModal({ orderId, totalOrder, flatItems, initialMethod, onClose }) {
        const [step, setStep] = useState(initialMethod === 'separadas' ? 'select-items' : 'pay');
        const [method, setMethod] = useState(initialMethod === 'separadas' ? null : initialMethod);
        const [selectedIds, setSelectedIds] = useState(new Set());

        // Propina para métodos simples (efectivo, tarjeta, transferencia)
        const [tipType, setTipType] = useState('none');
        const [tipValue, setTipValue] = useState('0');

        // Efectivo (no mixto): con cuánto paga -> cambio a entregar
        const [receivedAmount, setReceivedAmount] = useState('');

        // Pago mixto: monto en efectivo (el resto se calcula solo) + propina de cada parte
        const [cashAmount, setCashAmount] = useState('');
        const [cashTipType, setCashTipType] = useState('none');
        const [cashTipValue, setCashTipValue] = useState('0');
        const [cardTipType, setCardTipType] = useState('none');
        const [cardTipValue, setCardTipValue] = useState('0');

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

        function toggleItem(id) {
            setSelectedIds(prev => {
                const next = new Set(prev);
                if (next.has(id)) next.delete(id); else next.add(id);
                return next;
            });
        }

        const subtotalSeleccionado = flatItems
            .filter(it => selectedIds.has(it.id))
            .reduce((sum, it) => sum + it.lineTotal, 0);

        const baseAmount = initialMethod === 'separadas' ? subtotalSeleccionado : totalOrder;

        function confirmarPago(payload) {
            // ============================================================
            // TODO: acá va el fetch real al backend para registrar el pago,
            // ej:
            //
            // fetch('crud-payment.php', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ order: orderId, ...payload })
            // });
            //
            // Falta definir la estructura de la tabla de pagos para armar
            // esto bien (columnas de método, propina, monto, items pagados
            // en el caso de cuentas separadas, etc).
            // ============================================================
            console.log('Pago a registrar:', { orderId, ...payload });
            show_alert('Pago registrado', `Total cobrado: $${formatPrice(payload.total)}`);
            bootstrap.Modal.getInstance(document.getElementById('static-payment')).hide();
        }

        let title = 'Cobrar orden';
        let body = null;

        if (step === 'select-items') {
            title = 'Cuentas Separadas — Selecciona los Productos';
            body = (
                <div>
                    <ul class="list-group mb-3">
                        {flatItems.map(it => (
                            <li key={it.id} class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        checked={selectedIds.has(it.id)}
                                        onChange={() => toggleItem(it.id)}
                                        id={`chk-${it.id}`}
                                    />
                                    <label class="form-check-label" htmlFor={`chk-${it.id}`}>
                                        {it.qty > 1 ? `${it.qty}x ` : ''}{it.name}
                                    </label>
                                </div>
                                <span>${formatPrice(it.lineTotal)}</span>
                            </li>
                        ))}
                    </ul>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Subtotal seleccionado</span>
                        <span>${formatPrice(subtotalSeleccionado)}</span>
                    </div>
                    <button
                        class="btn btn-primary w-100"
                        disabled={selectedIds.size === 0}
                        onClick={() => setStep('choose-method')}
                    >
                        Continuar
                    </button>
                </div>
            );
        } else if (step === 'choose-method') {
            title = 'Cuentas Separadas — Método de Pago';
            body = (
                <div class="d-grid gap-2">
                    <div class="d-flex justify-content-between fw-bold mb-2">
                        <span>Subtotal a cobrar</span>
                        <span>${formatPrice(baseAmount)}</span>
                    </div>
                    <button class="btn btn-outline-success" onClick={() => { setMethod('efectivo'); setStep('pay'); }}>Efectivo</button>
                    <button class="btn btn-outline-danger" onClick={() => { setMethod('tarjeta'); setStep('pay'); }}>Tarjeta</button>
                    <button class="btn btn-outline-info text-dark" onClick={() => { setMethod('transferencia'); setStep('pay'); }}>Transferencia</button>
                    <button class="btn btn-outline-danger" onClick={() => { setMethod('mixto'); setStep('pay'); }}>Mixto</button>
                    <button class="btn btn-outline-dark" onClick={() => setStep('select-items')}>Volver a elegir productos</button>
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
                        <input
                            type="number" min="0" max={baseAmount} step="0.01"
                            value={cashAmount}
                            onChange={e => setCashAmount(e.target.value)}
                        />
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
                            method: 'mixto',
                            base: baseAmount,
                            details: { cash, cashTip, card, cardTip },
                            total: totalACobrar,
                            itemIds: initialMethod === 'separadas' ? Array.from(selectedIds) : null
                        })}>
                            Confirmar pago
                        </button>
                    </div>
                </div>
            );
        } else {
            const labels = { efectivo: 'Pago en efectivo', tarjeta: 'Pago con tarjeta', transferencia: 'Pago por transferencia' };
            title = labels[method] || 'Cobrar';
            const tip = calcTip(baseAmount, tipType, tipValue);
            const totalACobrar = baseAmount + tip;

            // Solo aplica para efectivo (no mixto, no tarjeta/transferencia)
            const received = Number(receivedAmount || 0);
            const cambio = received - totalACobrar;

            body = (
                <div class="subbody-detalles-cuenta">
                    <div class="container-detalles-cuenta">
                        <b>Cuenta</b>
                        <span>${formatPrice(baseAmount)}</span>
                    </div>

                    <TipInput base={baseAmount} tipType={tipType} tipValue={tipValue}
                        onTipTypeChange={setTipType} onTipValueChange={setTipValue} />

                    <div class="container-total-final">
                        <b>Total a cobrar</b>
                        <span>${formatPrice(totalACobrar)}</span>
                    </div>

                    {method === 'efectivo' && (
                        <React.Fragment>
                            <div class="container-input-recibido">
                                <label class="form-label">¿Con cuánto paga?</label>
                                <input
                                    type="number" min="0" step="0.01"
                                    value={receivedAmount}
                                    onChange={e => setReceivedAmount(e.target.value)}
                                />
                            </div>

                            {receivedAmount !== '' && (
                                cambio >= 0 ? (
                                    <div class="container-cambio">
                                        <b>Cambio a entregar</b>
                                        <span class="amount">${formatPrice(cambio)}</span>
                                    </div>
                                ) : (
                                    <div class="container-cambio">
                                        <b class="text-danger">Falta</b>
                                        <span class="amount text-danger">${formatPrice(Math.abs(cambio))}</span>
                                    </div>
                                )
                            )}
                        </React.Fragment>
                    )}

                    <div class="d-grid">
                        <button
                            class="btn-execute"
                            disabled={method === 'efectivo' && receivedAmount !== '' && cambio < 0}
                            onClick={() => confirmarPago({
                                method,
                                base: baseAmount,
                                tip,
                                total: totalACobrar,
                                received: method === 'efectivo' ? received : null,
                                change: method === 'efectivo' ? Math.max(cambio, 0) : null,
                                itemIds: initialMethod === 'separadas' ? Array.from(selectedIds) : null
                            })}>
                            Confirmar pago
                        </button>
                    </div>
                </div>
            );
        }

        return (
            <React.Fragment>
                {titleEl && ReactDOM.createPortal(title, titleEl)}
                {bodyEl && ReactDOM.createPortal(body, bodyEl)}
            </React.Fragment>
        );
    }

    function ItemsModal({ orderId, onClose }) {
        const [batches, setBatches] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState(null);
        const [paymentMethod, setPaymentMethod] = useState(null); // 'efectivo'|'tarjeta'|'transferencia'|'mixto'|'separadas'|null
        const [showDiscountModal, setShowDiscountModal] = useState(false);
        const [descuento, setDescuento] = useState(0);

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

        useEffect(() => {
            let cancelado = false;
            setLoading(true);
            fetchOrderItems(orderId)
                .then(data => { if (!cancelado) { setBatches(data); setError(null); } })
                .catch(e => { if (!cancelado) setError(e.message); })
                .finally(() => { if (!cancelado) setLoading(false); });
            return () => { cancelado = true; };
        }, [orderId]);

        const bodyEl = document.getElementById('orderinfo-body');
        const titleEl = document.getElementById('orderinfo-title');

        // Lista plana de items (para "Cuentas Separadas") + total general de la orden
        const flatItems = batches.flatMap(batch => batch.items.map(block => {
            const { unitTotal, lineTotal } = computeBlockTotal(block);
            return { id: block.id, name: block.name, qty: block.qty, unitTotal, lineTotal };
        }));
        const totalOrderBruto = flatItems.reduce((sum, it) => sum + it.lineTotal, 0);
        // El descuento nunca puede dejar el total en negativo
        const totalOrder = Math.max(totalOrderBruto - descuento, 0);

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
                    <span>Cargando lista</span>
                </div>
            </div>
        ) : (
            <div class="modal-items">
                {batches.map(batch => (
                    <div key={batch.batch_id} class="batch-block">
                        <div class="batch-header">
                            <span class="batch-number">{batch.batch_seq == 1 ? 'Creado' : 'Agregado despues'}</span>
                            <div class="batch-fecha">
                                <span class="me-2">{new Date(batch.batch_created_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit' })}</span>
                                <span>{new Date(batch.batch_created_at).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                        </div>

                        {batch.items.map(block => {
                            const { unitTotal, lineTotal } = computeBlockTotal(block);

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
                                                                <span><b class="text-danger">{`${it.qty} x`}</b> {it.name}{it.is_extra ? <span class="fz-tag text-white bg-primary rounded ms-1 p-0 ps-2 pe-2">Extra</span> : ''}</span>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                        <div class="d-flex align-items-start justify-content-center">
                                            <div class="qty-item">{block.qty}</div>
                                        </div>
                                    </div>
                                    {block.extras && block.extras.length > 0 && (
                                        <div class="item-extras">
                                            {block.extras.map(ex => (
                                                <div key={ex.id}>
                                                    <span>+ <b class="text-danger">{`${ex.qty} x`}</b> {ex.name}</span> <span class="price-unit">(${formatPrice(ex.price)})</span>
                                                    {ex.note && <div class="item-note">{ex.note}</div>}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                    {block.note && <div class="subcontainer-comments">{block.note}</div>}
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="price-unit m-0">${formatPrice(unitTotal)}</span>
                                        <span class="price-unit m-0 fw-bold">${formatPrice(lineTotal)}</span>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ))}

                {descuento > 0 && (
                    <div class="container-amount-total">
                        <span>Subtotal</span>
                        <span>${formatPrice(totalOrderBruto)}</span>
                    </div>
                )}
                {descuento > 0 && (
                    <div class="container-amount-total">
                        <span>Descuento</span>
                        <span>-${formatPrice(descuento)}</span>
                    </div>
                )}
                <div class="container-amount-total">
                    <b>Total</b>
                    <span>${formatPrice(totalOrder)}</span>
                </div>

                <div class="container-pay-options">
                    <button class="btn btn-outline-success" onClick={() => setPaymentMethod('efectivo')}>Pago en Efectivo</button>
                    <button class="btn btn-outline-danger" onClick={() => setPaymentMethod('tarjeta')}>Pago con Tarjeta</button>
                    <button class="btn btn-outline-info text-dark" onClick={() => setPaymentMethod('transferencia')}>Pago con Transferencia</button>
                    <button class="btn btn-outline-warning text-dark" onClick={() => setPaymentMethod('separadas')}>Cuentas Separadas</button>
                    <button class="btn btn-outline-danger" onClick={() => setPaymentMethod('mixto')}>Pago Mixto</button>
                    <button class="btn btn-outline-dark">Recibo</button>
                    <button class="btn btn-outline-dark" onClick={() => setShowDiscountModal(true)}>Descuento</button>
                </div>
            </div>
        );

        return (
            <React.Fragment>
                {titleEl && ReactDOM.createPortal(`Comanda #${orderId}`, titleEl)}
                {bodyEl && ReactDOM.createPortal(content, bodyEl)}
                {paymentMethod && (
                    <PaymentModal
                        orderId={orderId}
                        totalOrder={totalOrder}
                        flatItems={flatItems}
                        initialMethod={paymentMethod}
                        onClose={() => setPaymentMethod(null)}
                    />
                )}
                {showDiscountModal && (
                    <DiscountModal
                        currentDiscount={descuento}
                        maxAmount={totalOrderBruto}
                        onConfirm={setDescuento}
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
        const [lastUpdated, setLastUpdated] = useState(null);
        const [paused, setPaused] = useState(false);
        const [filtro, setFiltro] = useState('pendiente'); // coincide con el botón "Pendientes" ya disabled en el body
        const [selectedOrder, setSelectedOrder] = useState(null);
        const intervalRef = useRef(null);

        const loadOrders = useCallback(async () => {
            try {
                const data = await fetchPendingOrders();
                // Ya no se reordena acá: se respeta el orden que trae la API
                // (ORDER BY modified_at DESC en pending_orders()).
                setOrders(data);
                setError(null);
                setLastUpdated(new Date());
            } catch (e) {
                setError(e.message || 'Error al consultar órdenes');
            } finally {
                setLoading(false);
            }
        }, []);

        useEffect(() => {
            loadOrders();
        }, [loadOrders]);

        useEffect(() => {
            if (paused) {
                clearInterval(intervalRef.current);
                return;
            }
            intervalRef.current = setInterval(loadOrders, POLL_INTERVAL_MS);
            return () => clearInterval(intervalRef.current);
        }, [paused, loadOrders]);

        // Escucha el evento que disparan los botones de filtro (fuera de React, en el body).
        // FIX: además de cambiar el filtro, dispara un fetch inmediato — antes había que
        // esperar al próximo poll (hasta 5s) para ver datos actualizados.
        useEffect(() => {
            function handleFiltro(e) {
                setFiltro(e.detail);
                loadOrders();
            }
            window.addEventListener('filtro-comandas', handleFiltro);
            return () => window.removeEventListener('filtro-comandas', handleFiltro);
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
                return 0; // 'pendiente': se respeta el orden que ya trae la API (modified_at DESC)
            });

        return (
            <div class="app">
                <div class="title-comandas">
                    <div><span class="title"></span></div>

                    <div class="status-line">
                        {error && <span class="error-text">{error} - reintentando...</span>}
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
                    <ItemsModal orderId={selectedOrder} onClose={() => setSelectedOrder(null)} />
                )}
            </div>
        );
    }

    ReactDOM.createRoot(document.getElementById('root')).render(<App />);
</script>