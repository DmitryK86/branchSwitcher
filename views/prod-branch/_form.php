<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Project;
use app\models\Repository;

/* @var $this yii\web\View */
/* @var $model app\models\ProdBranch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prod-branch-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php
    if ($model->isNewRecord): ?>
        <?= $form->field($model, 'project_id')->dropDownList(
            ArrayHelper::map(Project::findAll(['enabled' => true]), 'id', 'name'),
            ['prompt' => ' -- ']
        ); ?>
        <?= $form->field($model, 'repository_id')->dropDownList(
            ArrayHelper::map(Repository::findAll(['enabled' => true]), 'id', 'name'),
            ['prompt' => ' -- ']
        ); ?>
    <?php
    endif; ?>

    <?= $form->field($model, 'branch_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
