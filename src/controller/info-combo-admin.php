<?php
include('../model/db.php');
$combo = $_POST['combo'];
$data = combo($combo);
$list_groups = groups_combo($combo);
?>

<div class="modulo-header">
    <div><span class="text-headline text-capitalize"><?= $data['combo'] ?></span></div>
    <div><button class="btn-add" onclick="arm_combo(<?= $combo ?>)">Agregar Sección</button>
    </div>
</div>
<hr>

<div class="container-fluid">
    <div>
        <?php
        if ($list_groups) {
            foreach ($list_groups as $g) {
                ?>
                <div class="card mt-1">
                    <div class="lh-2">
                        <div><b class="text-capitalize"><?= $g['group'] ?></b></div>
                        <div>
                            <p class="text-muted lh-1">
                                <?= $g['instruction'] ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php
                        $items = group_items($g['id']);
                        foreach ($items as $i) {
                            echo '<div class="d-flex align-items-center justify-content-between mb-1">';
                                echo '<div>' . $i['name'] . ($i['type'] == 'extra' ? '<span class="fz-tag text-white bg-primary rounded ms-1 p-0 ps-2 pe-2">Extra</span>' : '') . '</div>';
                                echo '<div class="d-flex">
                                    <div>
                                        <button class="btn-add object" onclick="product_less('.$i['id'].')"><i class="fi fi-br-minus-small"></i></button>
                                    </div>';
                                echo '<div class="d-flex align-items-center justify-content-center" style="width: 20px; margin: 0px 5px;"><span id="qty-'.$i['id'].'">' . $i['qty'] . '</span></div>';
                                echo '<div><button class="btn-add object" onclick="product_plus('.$i['id'].')"><i class="fi fi-br-plus"></i></button></div>
                                </div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <div class="mt-2 d-flex align-items-center justify-content-end">
                        <button class="btn-delete" onclick="delete_section(<?= $g['id'] ?>)">Eliminar</button>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="container-system-message">
                <i class="fi fi-br-restaurant"></i>
                <span>Aun no se han creado secciones para el combo</span>
            </div>';
        }
        ?>
    </div>
</div>