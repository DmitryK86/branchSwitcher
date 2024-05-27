<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProdBranch */

$this->title = 'Create Prod Branch';
$this->params['breadcrumbs'][] = ['label' => 'Prod Branches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prod-branch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
