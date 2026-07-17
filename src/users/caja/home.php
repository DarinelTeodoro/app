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
        return Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

    function ItemsModal({ orderId, onClose }) {
        const [batches, setBatches] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState(null);

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
                            // Suma de todos los extras (precio x cantidad de cada uno)
                            const totalExtras = (block.extras || []).reduce(
                                (sum, ex) => sum + Number(ex.price) * ex.qty,
                                0
                            );
                            // Precio base del item + todos sus extras
                            const totalBlock = Number(block.price) + totalExtras;
                            const totalFinal = totalBlock * block.qty;

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
                                        <span class="price-unit m-0">${formatPrice(totalBlock)}</span>
                                        <span class="price-unit m-0 fw-bold">${formatPrice(totalFinal)}</span>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ))}
                <div class="container-pay-options">
                    <button class="btn btn-outline-success">Pago en Efectivo</button>
                    <button class="btn btn-outline-danger">Pago con Tarjeta</button>
                    <button class="btn btn-outline-info text-dark">Pago con Transferencia</button>
                    <button class="btn btn-outline-warning text-dark">Cuentas Separadas</button>
                    <button class="btn btn-outline-danger">Pago Mixto</button>
                    <button class="btn btn-outline-dark">Recibo</button>
                    <button class="btn btn-outline-dark">Descuento</button>
                </div>
            </div>
        );

        return (
            <React.Fragment>
                {titleEl && ReactDOM.createPortal(`Comanda #${orderId}`, titleEl)}
                {bodyEl && ReactDOM.createPortal(content, bodyEl)}
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