<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SharedEnv;
use yii\helpers\ArrayHelper;
use app\models\Project;
use app\helpers\EnvUrlBuilder;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\forms\SharedEnvsForm */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $users User[] */

$this->title = 'Shared Envs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shared-env-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'project_id',
                'value' => function (SharedEnv $sharedEnv) {
                    return $sharedEnv->environment->project->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'project_id',
                    ArrayHelper::map(Project::findAll(['enabled' => true]), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                )
            ],
            [
                'attribute' => 'owner_id',
                'format' => 'html',
                'value' => function (SharedEnv $sharedEnv) {
                    return $sharedEnv->owner->username;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'owner_id',
                    ArrayHelper::map($users, 'id', 'username'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                ),
            ],
            [
                'attribute' => 'renter_id',
                'format' => 'html',
                'value' => function (SharedEnv $sharedEnv) {
                    return $sharedEnv->renter->username;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'renter_id',
                    ArrayHelper::map($users, 'id', 'username'),
                    ['class' => 'form-control', 'prompt' => 'Все']
                ),
                'visible' => \Yii::$app->user->getIdentity()->isRoot(),
            ],
            [
                'attribute' => 'environment_code',
                'format' => 'html',
                'value' => function (SharedEnv $sharedEnv) {
                    return $sharedEnv->environment->environment_code;
                }
            ],
            [
                'attribute' => 'branches',
                'format' => 'html',
                'value' => function (SharedEnv $sharedEnv) {
                    $result = [];
                    foreach ($sharedEnv->environment->branches as $branch) {
                        if (!$branch->repository->enabled) {
                            continue;
                        }
                        $result[] = "{$branch->repository->name}: <code>{$branch->branch}</code>";
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'label' => 'URL',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:100px'],
                'value' => function (SharedEnv $sharedEnv) {
                    $code = $sharedEnv->environment->environment_code;
                    if (!$code) {
                        return null;
                    }
                    $result = [];
                    foreach (\Yii::$app->params['stageSubdomainPrefixes'][$sharedEnv->environment->project->type] as $name => $prefix) {
                        if (!in_array($name, ['WEB', 'Admin'])) {
                            continue;
                        }
                        $url = EnvUrlBuilder::build($sharedEnv->environment, $name);
                        $result[] = "<a href='{$url}' target='_blank'>{$name}</a>";
                    }

                    return implode('<br>', $result);
                },
            ],
            [
                'attribute' => 'comment',
                'headerOptions' => ['style' => 'width: 200px'],
                'value' => function (SharedEnv $sharedEnv) {
                    return $sharedEnv->environment->comment;
                },
            ],
            [
                'attribute' => 'created_at',
                'value' => function (SharedEnv $sharedEnv) {
                    return date('Y-m-d H:i:s', strtotime($sharedEnv->environment->created_at));
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function (SharedEnv $sharedEnv) {
                    return date('Y-m-d H:i:s', strtotime($sharedEnv->environment->updated_at));
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ]
        ],
    ]); ?>


</div>
