<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $cars \common\models\Car[] */

$this->title = 'Cars';
?>
<div class="site-cars">
    <h1>Список машин</h1>

    <table border="1">
        <?php
            foreach ($cars as $car){
                $price = $car->price/100;
                echo "
<tr><td>
    <img width='480' height='360' src='/download-file/{$car->logo->path}'><br>
    <h2><a href='/admin/car/get/{$car->id}'>{$car->name}</a></h2>
    <b>{$price}</b><br>
    <b>{$car->color}</b>
</td></tr>";
            }
        ?>
    </table>
</div>