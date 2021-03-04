<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var backend\forms\CategoryForm $category_form */
?>

<?php $form = ActiveForm::begin() ?>
<?= $form->field($category_form, 'title') ?>
<?= $form->field($category_form, 'logo_id') ?>
<?= Html::submitButton('Send', ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end() ?>