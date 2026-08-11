<?php
class Conexion extends PDO
{
    private $tipo_de_base = 'mysql';
    private $host = 'localhost';
    private $nombre_de_base = 'db_app';
    private $usuario = 'root';
    private $contrasena = '';

    public function __construct()
    {
        try {
            parent::__construct($this->tipo_de_base . ':host=' . $this->host . ';dbname=' . $this->nombre_de_base, $this->usuario, $this->contrasena);
        } catch (PDOException $e) {
            echo "Ha surgido un error y no se puede conectar a la B.D. DETALLE: " . $e->getMessage();
        }
    }
}


// ***************************************************** USERS
function search_userid($user)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM user WHERE id = :id");
    $query->bindParam(':id', $user);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function search_username($user)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM user WHERE username = :user AND status = 1");
    $query->bindParam(':user', $user);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}


function list_users()
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM user WHERE status = 1 ORDER BY rol ASC, name ASC");
    $query->execute();
    $count = $query->rowCount();

    if ($count > 0) {
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

// ********************************************** ITEMS
function category($category)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM category WHERE id = :id");
    $query->bindParam(':id', $category);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function categories()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM category ORDER BY
        CASE 
            WHEN id = 1 THEN 1 
            ELSE 0 
        END, category ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function combo($combo)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM combo WHERE id = :id");
    $query->bindParam(':id', $combo);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function combos()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM combo ORDER BY combo ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function products_for_combo()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_arm_combo");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        return !empty($results) ? $results : false;
    } catch (PDOException $e) {
        return false;
    }
}

function groups_combo($combo)
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM combo_groups WHERE combo = :combo");
        $query->bindParam(':combo', $combo);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function group($group)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM combo_groups WHERE id = :id");
    $query->bindParam(':id', $group);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function item($item)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM combo_groups_products WHERE id = :id");
    $query->bindParam(':id', $item);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function group_items($group)
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_group_items WHERE combo_group = :group ORDER BY id ASC");
        $query->bindParam(':group', $group);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results;

    } catch (PDOException $e) {
        return false;
    }
}


function combo_items($combo_id)
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_group_items WHERE combo = :combo ORDER BY combo_group ASC, id ASC");
        $query->bindParam(':combo', $combo_id);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function product($combo)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM product WHERE id = :id");
    $query->bindParam(':id', $combo);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function products()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM product ORDER BY product ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function view_products()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_recipe_products
            ORDER BY 
            CASE 
                WHEN name_category = 'Extras' THEN 1
                WHEN name_category = 'Otros' THEN 1
                ELSE 0
            END ASC,
            name_category ASC,
            name ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}


function view_menu()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_menu
            ORDER BY 
            CASE 
                WHEN category_id = 0 THEN 0
                ELSE 1
            END ASC,
            category ASC,
            product ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return $results;

    } catch (PDOException $e) {
        return false;
    }
}

function variant($variant)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM variant WHERE id = :id");
    $query->bindParam(':id', $variant);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function variants()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM variant ORDER BY id ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function extra($extra)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM extra WHERE id = :id");
    $query->bindParam(':id', $extra);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function extras()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM extra ORDER BY id ASC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}



// ******************************************************** INVENTORY

function materia($materia)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM materia WHERE id = :id");
    $query->bindParam(':id', $materia);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function materias($filtro)
{
    $conexion = new Conexion();

    try {
        if ($filtro == 'ingredient' || $filtro == 'primary') {
            $query = $conexion->prepare("SELECT * FROM materia WHERE type = :type ORDER BY materia ASC");
            $query->bindParam('type', $filtro);
        } else {
            $query = $conexion->prepare("SELECT * FROM materia ORDER BY materia ASC");
        }
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}


function adjustment($adjustment)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM materia_adjustment WHERE id = :id");
    $query->bindParam(':id', $adjustment);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function adjustments()
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM materia_adjustment ORDER BY date DESC");
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}
function purchases($id)
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_materia_purchases WHERE adjustment = :adjustment ORDER BY type DESC, id ASC");
        $query->bindParam(':adjustment', $id);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        return false;
    }
}

function recipe($id, $type)
{
    $conexion = new Conexion();

    try {
        $query = $conexion->prepare("SELECT * FROM view_items_recipe WHERE product = :product AND type_product = :type");
        $query->bindParam(':product', $id);
        $query->bindParam(':type', $type);
        $query->execute();

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        #echo "Filas encontradas: " . count($results) . "<br>";

        return !empty($results) ? $results : false;

    } catch (PDOException $e) {
        #echo "ERROR SQL: " . $e->getMessage() . "<br>";
        return false;
    }
}



function do_recipe_main($order, $destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM view_items WHERE sale_order = :order AND (destination = :destination OR destination = 'ambos') AND realized = 0");
    $query->bindParam(':order', $order);
    $query->bindParam(':destination', $destination);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return !empty($results) ? $results : false;
}


function do_recipe_combo($item, $destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM view_combo_item_selected WHERE item = :item AND destination = :destination AND realized = 0");
    $query->bindParam(':item', $item);
    $query->bindParam(':destination', $destination);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return !empty($results) ? $results : false;
}


function do_recipe_extra($item, $destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM items_extras WHERE id_item = :item AND destination = :destination AND realized = 0");
    $query->bindParam(':item', $item);
    $query->bindParam(':destination', $destination);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return !empty($results) ? $results : false;
}


function check_discount($item, $type)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM view_items_recipe WHERE product = :item AND type_product = :tipo");
    $query->bindParam(':item', $item);
    $query->bindParam(':tipo', $type);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return !empty($results) ? $results : false;
}


function discount($materia, $value)
{
    $conexion = new Conexion();
    $update = $conexion->prepare("UPDATE materia SET total = total - :total WHERE id = :materia");
    $update->bindParam(':materia', $materia);
    $update->bindParam(':total', $value);
    $update->execute();
}


function insert_move_inventory($tabla, $forean, $order, $main, $item, $qty, $id_materia, $value)
{
    $conexion = new Conexion();
    $insert = $conexion->prepare("INSERT INTO history_inventory(tabla, forean, comanda, item_main, item, qty, materia, amount) VALUES (:tabla, :forean, :order, :item_main, :item, :qty, :materia, :amount)");
    $insert->bindParam(':tabla', $tabla);
    $insert->bindParam(':forean', $forean);
    $insert->bindParam(':order', $order);
    $insert->bindParam(':item_main', $main);
    $insert->bindParam(':item', $item);
    $insert->bindParam(':qty', $qty);
    $insert->bindParam(':materia', $id_materia);
    $insert->bindParam(':amount', $value);
    $insert->execute();
}



/**************************************************************TABLES */
function settings($keyword)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM settings WHERE keyword = :kw");
    $query->bindParam(':kw', $keyword);
    $query->execute();
    $count = $query->rowCount();

    if ($count == 1) {
        return $query->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    }
}

function tables_disabled()
{
    $conexion = new Conexion();
    $query = $conexion->prepare("
        SELECT DISTINCT n_table 
        FROM sale_order 
        WHERE status NOT IN ('finalizado', 'cancelado')
    ");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_COLUMN);
}




/******************************************************* ORDERS */

function data_pays_order($order, $init, $finish)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM transactions WHERE orden = :orden AND date >= :date_init AND date < :date_finish");
    $query->bindParam(':orden', $order);
    $query->bindParam(':date_init', $init);
    $query->bindParam(':date_finish', $finish);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function data_order($order)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM view_order WHERE id = :id");
    $query->bindParam(':id', $order);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}
function pending_orders()
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM view_order
            ORDER BY modified_at DESC");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function order_items($order)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM view_items
            WHERE sale_order = :order");
    $query->bindParam(':order', $order);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function item_extras($item)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM items_extras
            WHERE id_item = :item");
    $query->bindParam(':item', $item);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


function extras_ordered($item, $destino)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM items_extras
            WHERE id_item = :item AND destination = :destination");
    $query->bindParam(':item', $item);
    $query->bindParam(':destination', $destino);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


function combo_selected_items($combo_item_id)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM combo_item_selected WHERE item = :forean ORDER BY group_item");
    $query->bindParam(':forean', $combo_item_id);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


function get_destination($type, $id)
{
    $conexion = new Conexion();

    if ($type == 'extra') {
        $query = $conexion->prepare("SELECT destination FROM extra WHERE id = :id");
        $query->bindParam(':id', $id);
    } else {
        $query = $conexion->prepare("
            SELECT c.destination
            FROM product p
            JOIN category c ON p.category = c.id
            WHERE p.id = :id
        ");
        $query->bindParam(':id', $id);
    }

    $query->execute();
    $destination = $query->fetch(PDO::FETCH_ASSOC);

    return $destination ? $destination['destination'] : false;
}




/*************************************  Barra & Cocina */
function comandas($destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM view_order
            WHERE $destination = 1
            ORDER BY modified_at ASC");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function items_destination($order, $destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT *
            FROM view_items
            WHERE sale_order = :order
            AND (destination = :destination OR destination = 'ambos')
            AND realized = 0");
    $query->bindParam(':order', $order);
    $query->bindParam(':destination', $destination);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function view_combo_selected($combo_item_id, $destination)
{
    $conexion = new Conexion();
    $query = $conexion->prepare("SELECT * FROM view_combo_item_selected WHERE item = :forean AND destination = :destination AND realized = 0 ORDER BY group_item");
    $query->bindParam(':forean', $combo_item_id);
    $query->bindParam(':destination', $destination);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
?>