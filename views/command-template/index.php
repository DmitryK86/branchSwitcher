<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\CommandTemplate;
use app\models\Project;
use yii\helpers\ArrayHelper;
use app\managers\CommandBuilder;

/* @var $this yii\web\View */
/* @var $searchModel app\models\forms\CommandTemplateSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Command Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="command-template-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Command Template', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [
                'attribute' => 'action',
                'value' => function (CommandTemplate $template) {
                    return $template->action;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'action',
                    CommandBuilder::getActions(),
                    ['class' => 'form-control', 'prompt' => 'Все']
                )
            ],
            [
                'attribute' => 'project_id',
                'value' => function (CommandTemplate $template) {
                    return $template->project->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'project_id',
                    ArrayHelper::map(Project::findAll(['enabled' => true]), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                )
            ],
            'enabled:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
