<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProdBranch */

$this->title = 'Update Prod Branch: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prod Branches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prod-branch-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
