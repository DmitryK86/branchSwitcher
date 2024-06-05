<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\UserEnvironments;
use app\models\Project;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\helpers\EnvUrlBuilder;

/* @var $this yii\web\View */
/* @var $searchModel app\models\forms\UserEnvironmentsSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Environments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-environments-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Environment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $columns = [['class' => 'yii\grid\SerialColumn'],];
    if (Yii::$app->getUser()->getIdentity()->isRoot()) {
        $columns[] = [
            'attribute' => 'user_id',
            'value' => function (UserEnvironments $data) {
                return $data->user->username;
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'user_id',
                ArrayHelper::map(User::find()->andWhere(['status' => User::STATUS_ACTIVE])->orderBy('username')->all(), 'id', 'username'),
                ['class' => 'form-control', 'prompt' => 'Все']
            )
        ];
    }
    $columns = array_merge($columns, [
        [
            'attribute' => 'project_id',
            'value' => function (UserEnvironments $data) {
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
            'attribute' => 'id',
            'visible' => Yii::$app->getUser()->getIdentity()->isRoot(),
        ],
        'environment_code',
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function (UserEnvironments $data) {
                $statuses = UserEnvironments::getStatuses();
                $statusClass = UserEnvironments::getStatusClass($data->status);
                return "<span class='label label-{$statusClass}'>{$statuses[$data->status]}</span>" ?? 'n\a';
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'status',
                UserEnvironments::getStatuses(),
                ['class' => 'form-control', 'prompt' => 'Все']
            )
        ],
        [
            'attribute' => 'branches',
            'format' => 'html',
            'value' => function (UserEnvironments $data) {
                $result = [];
                foreach ($data->branches as $branch) {
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
            'value' => function (UserEnvironments $data) {
                $code = $data->environment_code;
                if (!$code) {
                    return null;
                }
                $result = [];
                $domain = \Yii::$app->params['stageDomain'];
                foreach (\Yii::$app->params['stageSubdomainPrefixes'][$data->project->type] as $name => $prefix) {
                    if (!in_array($name, ['WEB', 'Admin'])) {
                        continue;
                    }
                    $url = EnvUrlBuilder::build($data, $name);
                    $result[] = "<a href='{$url}' target='_blank'>{$name}</a>";
                }

                return implode('<br>', $result);
            },
        ],
        [
            'attribute' => 'comment',
            'headerOptions' => ['style' => 'width: 200px'],
            'value' => function (UserEnvironments $data) {
                return $data->comment;
            },
        ],
        [
            'attribute' => 'created_at',
            'value' => function (UserEnvironments $data) {
                return date('Y-m-d H:i:s', strtotime($data->created_at));
            },
        ],
        [
            'attribute' => 'updated_at',
            'value' => function (UserEnvironments $data) {
                return date('Y-m-d H:i:s', strtotime($data->updated_at));
            },
        ],
        [
            'attribute' => 'is_persist',
            'value' => function (UserEnvironments $data) {
                return $data->is_persist;
            },
            'visible' => Yii::$app->getUser()->getIdentity()->isRoot(),
            'format' => 'boolean',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
        ]
    ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>


</div>
