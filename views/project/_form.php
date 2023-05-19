<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Repository;
use app\models\Project;

/* @var $this yii\web\View */
/* @var $model app\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'params')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'repositories_id')->checkboxList(ArrayHelper::map(Repository::findAll(['enabled' => true]), 'id', 'name')) ?>

    <?= $form->field($model, 'enabled')->checkbox(['checked' => 'checked']) ?>

    <?= $form->field($model, 'type')->dropDownList(Project::PROJECT_TYPES) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
