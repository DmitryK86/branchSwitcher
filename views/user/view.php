<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($model->username) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'role',
            'username',
            'auth_key',
            'email:email',
            [
                'label' => 'Status',
                'value' => function(User $user){
                    return User::getStatusesArray()[$user->status] ?? 'n/a';
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'label' => 'Group',
                'format' => 'html',
                'value' => function(User $user){
                    return Html::tag('code', $user->group->name ?? null);
                },
            ],
        ],
    ]) ?>

</div>
