<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\forms\SwitchLogSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Switch Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="switch-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user_id',
                'value' => 'user.username',
                'filter' => ArrayHelper::map(User::find()->asArray()->all(), 'id', 'username'),
            ],
            'alias',
            'project',
            'from_branch',
            'to_branch',
            'status',
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i:s']
            ],
        ],
    ]); ?>


</div>
