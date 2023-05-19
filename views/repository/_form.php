<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Repository */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="repository-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'default_branch_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_token')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'version_control_provider')->textInput() ?>

    <?= $form->field($model, 'enabled')->checkbox(['checked' => 'checked']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
