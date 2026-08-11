/*Vista 1*/

CREATE VIEW `vista_productos` AS
SELECT
    COALESCE(v.product, p.id)                              AS product,
    v.id                                                     AS variant,
    NULL                                                     AS extra,
    CASE 
        WHEN v.id IS NOT NULL 
            THEN CONCAT(p.product, ' (', v.variant, ')') 
        ELSE p.product 
    END                                                      AS name,
    p.category                                               AS category,
    c.category                                               AS name_category,
    CASE 
        WHEN v.id IS NOT NULL THEN 'variante' 
        ELSE 'producto' 
    END                                                      AS type
FROM db_app.product p
LEFT JOIN db_app.category c ON p.category = c.id
LEFT JOIN db_app.variant v  ON v.product = p.id

UNION ALL

SELECT
    NULL          AS product,
    NULL          AS variant,
    e.id          AS extra,
    e.extra       AS name,
    0             AS category,
    'Extras'      AS name_category,
    'extra'       AS type
FROM db_app.extra e;





/*Vista 2*/

CREATE OR REPLACE VIEW view_combo_item_selected AS
SELECT
    cis.id              AS id,
    cis.combo           AS combo,
    cis.item            AS item,
    cis.type_item       AS type_item,
    cis.name_item       AS name_item,
    cis.group_item      AS group_item,
    cis.name_group_item AS name_group_item,
    cis.qty             AS qty,
    cis.forean          AS forean,
    cis.destination     AS destination,,
    cis.realized        AS realized
FROM db_app.combo_item_selected cis;






/*Vista 3*/
CREATE VIEW `vista_combo_groups_products` AS
SELECT
    cgp.id                AS id,
    cp.id                  AS combo_id,
    cgp.combo_group         AS combo_group,
    cp.combo                 AS combo,
    cp.group                  AS group_name,
    cp.type                    AS type_group,
    cp.instruction               AS instruction,
    cgp.product                   AS product,
    cgp.variant                    AS variant,
    cgp.extra                       AS extra,
    cgp.type                         AS type,
    cgp.qty                           AS qty,
    CASE 
        WHEN cgp.type = 'variante' THEN CONCAT(p2.product, ' (', v.variant, ')')
        WHEN cgp.type = 'extra'    THEN e.extra
        ELSE p.product
    END                                 AS name
FROM db_app.combo_groups_products cgp
LEFT JOIN db_app.combo_groups cp 
    ON cgp.combo_group = cp.id
LEFT JOIN db_app.product p 
    ON cgp.product = p.id 
    AND cgp.type = 'producto'
LEFT JOIN db_app.variant v 
    ON cgp.variant = v.id 
    AND cgp.type = 'variante'
LEFT JOIN db_app.product p2 
    ON v.product = p2.id
LEFT JOIN db_app.extra e 
    ON cgp.extra = e.id 
    AND cgp.type = 'extra';






/*Vista 4*/
CREATE OR REPLACE VIEW `view_items` AS
SELECT
    i.id                AS id,
    i.batch              AS batch,
    i.sale_order          AS sale_order,
    i.type                 AS type,
    i.product_id             AS product_id,
    i.variant_id               AS variant_id,
    i.combo_id                   AS combo_id,
    i.name                             AS name,
    i.qty                                AS qty,
    i.price_unit                           AS price_unit,
    i.total                                  AS total,
    i.note                                     AS note,
    i.added_at                                   AS added_at,
    i.destination                                  AS destination,
    i.realized                                       AS realized,
    i.payed                                            AS payed,
    b.seq                                                AS seq,
    b.created                                              AS created,
    b.finished                                               AS finished
FROM db_app.items i
LEFT JOIN db_app.batch b 
    ON i.batch = b.id;





/*Vista 5*/
CREATE VIEW `vista_recipe` AS
SELECT
    r.product        AS product,
    p.product          AS name_product,
    r.type_product       AS type_product,
    r.materia               AS materia,
    m.materia                 AS name_materia,
    r.type_materia               AS type_materia,
    r.value                        AS value,
    m.presentation                   AS presentation,
    m.metric                           AS metric
FROM db_app.recipe r
LEFT JOIN db_app.product p 
    ON r.product = p.id
LEFT JOIN db_app.materia m 
    ON r.materia = m.id;





/*Vista 6*/
CREATE VIEW `vista_materia_purchases` AS
SELECT
    m.materia        AS item,
    m.type             AS type,
    mp.id                AS id,
    mp.adjustment          AS adjustment,
    mp.materia                AS materia,
    mp.qty                      AS qty,
    mp.units                      AS units,
    mp.amount                       AS amount
FROM db_app.materia_purchases mp
LEFT JOIN db_app.materia m 
    ON mp.materia = m.id;





/*Vista 7*/
CREATE VIEW `vista_productos_combos` AS
SELECT
    p.id            AS id,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM db_app.variant v 
            WHERE v.product = p.id 
            LIMIT 1
        ) THEN 'variante' 
        ELSE 'producto' 
    END               AS type,
    p.product           AS product,
    c.destination         AS destination,
    p.description           AS description,
    p.img                     AS img,
    p.price                    AS price,
    p.category                   AS category_id,
    c.category                     AS category,
    p.available                      AS available
FROM db_app.product p
LEFT JOIN db_app.category c 
    ON p.category = c.id
WHERE p.category IS NULL 
   OR p.category <> 1

UNION ALL

SELECT
    c.id         AS id,
    'combo'        AS type,
    c.combo          AS product,
    NULL               AS destination,
    c.description        AS description,
    'default.webp'          AS img,
    c.price                   AS price,
    0                            AS category_id,
    'Combos'                       AS category,
    c.available                      AS available
FROM db_app.combo c;





/*Vista 8*/
CREATE OR REPLACE VIEW `view-order` AS
SELECT
    so.id AS id,
    so.delivery AS delivery,
    so.n_table AS n_table,
    so.client AS client,
    so.waiter AS waiter,
    so.status AS status,
    so.created_at AS created_at,
    so.modified_at AS modified_at,
    so.finished_at AS finished_at,
    so.deposit AS deposit,
    so.note AS note,

    -- total del producto + (suma de sus extras) x cantidad del producto
    COALESCE(SUM(i.total + COALESCE(ie.extras_total, 0) * i.qty), 0) AS total,

    u.name AS name,

    -- CAMBIO: realized=0 es pendiente, realized=1 es terminado (antes era 1/2)
    -- 0 = sin productos para esa estación
    -- 1 = hay al menos uno pendiente (realized=0)
    -- 2 = todos están terminados (realized=1)
    MAX(est.cocina) AS cocina,
    MAX(est.barra) AS barra

FROM db_app.items i
LEFT JOIN (
    SELECT id_item, SUM(total) AS extras_total
    FROM db_app.items_extras
    GROUP BY id_item
) ie ON ie.id_item = i.id
LEFT JOIN db_app.sale_order so
    ON so.id = i.sale_order
LEFT JOIN db_app.user u
    ON so.waiter = u.id
LEFT JOIN (
    SELECT
        comanda,
        CASE
            WHEN SUM(CASE WHEN destination = 'cocina' THEN 1 ELSE 0 END) = 0 THEN 0
            WHEN SUM(CASE WHEN destination = 'cocina' AND realized = 0 THEN 1 ELSE 0 END) > 0 THEN 1
            WHEN SUM(CASE WHEN destination = 'cocina' AND realized <> 1 THEN 1 ELSE 0 END) = 0 THEN 2
            ELSE 1
        END AS cocina,
        CASE
            WHEN SUM(CASE WHEN destination = 'barra' THEN 1 ELSE 0 END) = 0 THEN 0
            WHEN SUM(CASE WHEN destination = 'barra' AND realized = 0 THEN 1 ELSE 0 END) > 0 THEN 1
            WHEN SUM(CASE WHEN destination = 'barra' AND realized <> 1 THEN 1 ELSE 0 END) = 0 THEN 2
            ELSE 1
        END AS barra
    FROM db_app.items_destination
    GROUP BY comanda
) est ON est.comanda = so.id
WHERE i.payed = 0
GROUP BY so.id;






/*Vista 9*/
CREATE VIEW `vista_productos_con_receta` AS
SELECT
    pv.id           AS id,
    pv.name           AS name,
    pv.category         AS category,
    c.category            AS name_category,
    'producto'              AS origin,
    pv.type                   AS type,
    COALESCE(r.recipe, 0)       AS recipe
FROM (
    SELECT
        COALESCE(v.id, p.id)  AS id,
        CASE 
            WHEN v.id IS NOT NULL THEN CONCAT(p.product, ' (', v.variant, ')')
            ELSE p.product
        END                     AS name,
        p.category                AS category,
        CASE 
            WHEN v.id IS NOT NULL THEN 'variante'
            ELSE 'producto'
        END                          AS type
    FROM db_app.product p
    LEFT JOIN db_app.variant v 
        ON v.product = p.id
) pv
LEFT JOIN db_app.category c 
    ON pv.category = c.id
LEFT JOIN (
    SELECT
        recipe.product        AS product,
        recipe.type_product      AS type_product,
        COUNT(0)                    AS recipe
    FROM db_app.recipe
    GROUP BY recipe.product, recipe.type_product
) r 
    ON r.product = pv.id 
    AND r.type_product = pv.type

UNION ALL

SELECT
    e.id                AS id,
    e.extra               AS name,
    NULL                    AS category,
    'Extras'                  AS name_category,
    'extra'                     AS origin,
    'extra'                       AS type,
    COALESCE(r2.recipe, 0)          AS recipe
FROM db_app.extra e
LEFT JOIN (
    SELECT
        recipe.product        AS product,
        recipe.type_product      AS type_product,
        COUNT(0)                    AS recipe
    FROM db_app.recipe
    GROUP BY recipe.product, recipe.type_product
) r2 
    ON r2.product = e.id 
    AND r2.type_product = 'extra';






CREATE OR REPLACE VIEW items_destination AS
SELECT
	id,
    sale_order AS comanda,
    name AS item,
    destination,
    realized,
    'item' AS tabla
FROM items
WHERE destination <> 'Ambos'

UNION ALL

SELECT
	id,
    orden AS comanda,
    name AS item,
    destination,
    realized,
    'extra' AS tabla
FROM items_extras

UNION ALL

SELECT
	id,
    id_order AS comanda,
    name_item AS item,
    destination,
    realized,
    'combo' AS tabla
FROM combo_item_selected;