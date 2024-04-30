<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Project;
use yii\helpers\ArrayHelper;
use app\managers\CommandBuilder;

/* @var $this yii\web\View */
/* @var $model app\models\CommandTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="command-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->dropDownList(CommandBuilder::getActions(), ['id' => 'action', 'prompt'=>'Выбрать действие'] ) ?>

    <?= $form->field($model, 'project_id')->dropDownList(ArrayHelper::map(Project::find()->all(), 'id', 'name'), ['id' => 'project', 'prompt'=>'Выбрать проект'] ) ?>

    <?= $form->field($model, 'template')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'enabled')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
