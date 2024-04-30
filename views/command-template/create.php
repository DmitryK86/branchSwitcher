<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommandTemplate */

$this->title = 'Create Command Template';
$this->params['breadcrumbs'][] = ['label' => 'Command Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="command-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
