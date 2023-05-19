<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserEnvironments */
/* @var $availableProjects app\models\Project[] */

$this->title = 'Create User Environments';
$this->params['breadcrumbs'][] = ['label' => 'User Environments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-environments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'availableProjects' => $availableProjects,
    ]) ?>

</div>
