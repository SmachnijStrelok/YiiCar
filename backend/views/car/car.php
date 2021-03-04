<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $car \common\models\Car */

$this->title = 'Cars';
?>
<div class="site-cars">
    <h1>Подробная информация об автомобиле</h1>

    <table>
        <?php
                $price = $car->price/100;
                echo "
<tr><td>
    <img width='480' height='360' src='/download-file/{$car->logo->path}'><br>
    <h2>{$car->name}</h2>
    <b>Цена: </b><span>{$price} p</span><br>
    <b>Цвет: </b><span>{$car->color}</span><br>
    <b>Комплектация: </b><span>{$car->equipment}</span><br>
    <b>Доступность: </b><span>{$car->availability}</span><br>
    <b>Характеристики: </b><span>{$car->description}</span><br>
</td></tr>";
        ?>
    </table>
</div>