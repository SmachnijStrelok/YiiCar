<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $categories \common\models\CarCategory[] */

$this->title = 'Categories';
?>
<h2>asdf</h2>
<div class="site-categories">
    <h1>Список категорий</h1>

    <table style="border: solid">
        <?php
            foreach ($categories as $category){
                echo "
<tr><td>
<h2><a href='/admin/car/in_category/{$category->id}'>{$category->title}</a></h2>
<img width='480' height='360' src='/download-file/{$category->logo->path}'>
</td></tr>";
            }
        ?>
    </table>
</div>

