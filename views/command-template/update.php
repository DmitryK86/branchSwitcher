<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommandTemplate */

$this->title = 'Update Command Template: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Command Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="command-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
