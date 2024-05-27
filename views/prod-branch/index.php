<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\ProdBranch;
use yii\helpers\ArrayHelper;
use app\models\Project;
use app\models\Repository;

/* @var $this yii\web\View */
/* @var $searchModel app\models\forms\ProdBranchSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prod Branches';
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = !Yii::$app->user->identity->isUser();
?>
<div class="prod-branch-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($canUpdate):?>
    <p>
        <?= Html::a('Create Prod Branch', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif;?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $columns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'project_id',
                'value' => function (ProdBranch $data) {
                    return $data->project->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'project_id',
                    ArrayHelper::map(Project::findAll(['enabled' => true]), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                )
            ],
            [
                'attribute' => 'repository_id',
                'value' => function (ProdBranch $data) {
                    return $data->repository->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'repository_id',
                    ArrayHelper::map(Repository::findAll(['enabled' => true]), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                )
            ],
            'branch_name',
            [
                'attribute' => 'updated_at',
                'value' => function (ProdBranch $data) {
                    return date('Y-m-d H:i:s', strtotime($data->updated_at));
                },
            ],
        ];
    if ($canUpdate) {
        $columns = array_merge($columns, [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ]
        ]);
    }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns
    ]); ?>
</div>
