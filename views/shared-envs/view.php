<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\SharedEnv;
use app\helpers\EnvUrlBuilder;
use app\models\UserEnvironments;
use app\helpers\YesNoHelper;
use yii\helpers\ArrayHelper;
use app\helpers\LogHelper;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\SharedEnv */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shared Envs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$updateOneBranchButtons = [];
?>
<div class="shared-env-view">

    <h1><?= Html::encode("{$model->environment->project->name} env (owner {$model->environment->user->username})") ?></h1>

    <p>
        <?php if ($model->environment->canBeUpdated()):?>
            <?= Html::button('Update', ['class' => 'btn btn-primary', 'type' => 'button', 'id' => 'update']) ?>
        <?php endif;?>
    <div id="repo-branches" style="display: none">
        <?php $form = ActiveForm::begin(['id' => 'create-form', 'validateOnSubmit' => false, 'action' => [Url::toRoute(['update', 'id' => $model->id])]]); ?>
        <?php foreach ($model->environment->branches as $branchData): ?>
            <?php if (!$branchData->repository->enabled) {
                continue;
            } ?>
            <?php $updateOneBranchButtons[] = Html::a("Update {$branchData->repository->code}", "update-one?id={$model->id}&repositoryCode={$branchData->repository->code}&branchName={$branchData->branch}", ['class' => 'btn btn-success']);?>
        <?php endforeach;?>
        <div class="form-group">
            <?php
            if (count($updateOneBranchButtons) > 1): ?>
                <?= Html::submitButton('Update all', ['class' => 'btn btn-success']) ?>
                <?= implode(' ', $updateOneBranchButtons); ?>
            <?php
            else: ?>
                <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
            <?php
            endif; ?>
        </div>
        <?php ActiveForm::end();?>
    </div>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Project',
                'value' => function(SharedEnv $sharedEnv){
                    return $sharedEnv->environment->project->name;
                },
            ],
            [
                'format' => 'raw',
                'label' => 'URL-s',
                'value' => function(SharedEnv $sharedEnv){
                    $code = $sharedEnv->environment->environment_code;
                    if (!$code) {
                        return null;
                    }
                    $result = [];
                    foreach (\Yii::$app->params['stageSubdomainPrefixes'][$sharedEnv->environment->project->type] as $name => $prefix) {
                        $url = EnvUrlBuilder::build($sharedEnv->environment, $name);
                        $name = sprintf('%s (%s)', $name, $url);
                        $result[] = "<a href='{$url}' target='_blank'>{$name}</a>";
                    }

                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Connection string',
                'value' => function(SharedEnv $sharedEnv){
                    $code = $sharedEnv->environment->environment_code;
                    if (!$code) {
                        return null;
                    }

                    return str_replace('CODE', $code, \Yii::$app->params['connectionString'] ?? '');
                },
                'visible' => !empty(Yii::$app->getUser()->getIdentity()->ssh_key),
            ],
            [
                'format' => 'html',
                'label' => 'Status',
                'value' => function(SharedEnv $sharedEnv){
                    $statuses = UserEnvironments::getStatuses();
                    $statusClass = UserEnvironments::getStatusClass($sharedEnv->environment->status);
                    return "<span class='label label-{$statusClass}'>{$statuses[$sharedEnv->environment->status]}</span>" ?? 'n\a';
                },
            ],
            [
                'format' => 'html',
                'label' => 'Branches',
                'value' => function(SharedEnv $sharedEnv){
                    $result = [];
                    foreach ($sharedEnv->environment->branches as $branch) {
                        $result[] = "{$branch->repository->name}: <code>{$branch->branch}</code>";
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Created At',
                'value' => function(SharedEnv $sharedEnv){
                    return date('Y-m-d H:i:s', strtotime($sharedEnv->environment->created_at));
                },
            ],
            [
                'format' => 'html',
                'label' => 'Updated At',
                'value' => function(SharedEnv $sharedEnv){
                    return date('Y-m-d H:i:s', strtotime($sharedEnv->environment->updated_at));
                },
            ],
            [
                'format' => 'raw',
                'label' => 'Comment',
                'value' => function(SharedEnv $sharedEnv){
                    return $sharedEnv->environment->comment;
                },
            ],
            [
                'format' => 'html',
                'label' => 'Related services',
                'value' => function(SharedEnv $sharedEnv){
                    $result = [];
                    foreach ($sharedEnv->environment->relatedServices as $serviceEnv) {
                        $url = EnvUrlBuilder::build($serviceEnv, EnvUrlBuilder::TYPE_ADMIN);
                        $result[] = Html::a($serviceEnv->project->name . " ({$url})", $url, ['target' => '_blank']);
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Added users',
                'value' => function(SharedEnv $sharedEnv){
                    $result = [];
                    foreach ($sharedEnv->environment->addedUsers as $user) {
                        $result[] = $user->username;
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'raw',
                'label' => 'Remove basic auth',
                'value' => function(SharedEnv $sharedEnv){
                    if ($sharedEnv->environment->basic_auth_removed_till) {
                        return date('Y-m-d H:i:s', strtotime($sharedEnv->environment->basic_auth_removed_till));
                    }
                    if (!$sharedEnv->environment->isReady()) {
                        return null;
                    }
                    $content = Html::input('number', null, null, ['class' => 'form-control', 'style' => 'width:100px', 'min' => 10, 'max' => 60, 'placeholder' => 'minutes', 'id' => 'basic-auth-remove-minutes']);
                    $content .= Html::button('Open', ['class' => 'btn btn-success', 'id' => 'basic-auth-remove-btn']);
                    return Html::tag('div', $content, ['style' => 'display:flex;']);
                },
            ],
            [
                'format' => 'raw',
                'label' => 'Run autotest',
                'value' => function(SharedEnv $sharedEnv){
                    return YesNoHelper::getValue($sharedEnv->environment->is_run_autotest);
                },
                'visible' => !$model->environment->project->isServiceProject(),
            ],
        ],
    ]) ?>

    <p>
        <?php
        if ($model->environment->isInProgress()): ?>
            <?= Html::a('Status', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        endif; ?>

        <?php
        if ($model->environment->isReady()): ?>
        <?php
        $form = ActiveForm::begin(['id' => 'create-form', 'validateOnSubmit' => false, 'action' => [Url::toRoute(['add-key', 'envId' => $model->id])]]); ?>

        <?= $form->field($model->environment, 'added_users_keys')->dropDownList(
            ArrayHelper::map(User::find()->where("coalesce(ssh_key, '') <> '' AND status = :status", [':status' => User::STATUS_ACTIVE])->andWhere(['not in', 'id', $model->environment->getAddedUsersKeys()])->orderBy('username')->all(), 'id', 'username'),
            ['multiple' => true, 'size' => 15]
        )->label('Add users ssh key to this env'); ?>

    <div class="form-group">
        <?= Html::submitButton('Add', ['class' => 'btn btn-info']) ?>
    </div>

<?php
ActiveForm::end(); ?>

<?php
endif; ?>
    </p>

    <?php
    if ($model->environment->isError() && ($logData = LogHelper::getLogData($model->environment)) && $logData->isLogExist()): ?>
        <p>
        <h3>
            <?= $logData->getFileName(); ?>
        </h3>
        <code>
            <?= $logData->getContent(); ?>
        </code>
        </p>
    <?php
    endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        $('#update').on('click', function () {
            $('#repo-branches').slideToggle();
        });

        $('#basic-auth-remove-minutes').on('input', function () {
            let val = $(this).val();
            if (val > <?= UserEnvironments::MAX_REMOVE_AUTH_MINUTES;?>) {
                val = <?= UserEnvironments::MAX_REMOVE_AUTH_MINUTES;?>
            }

            $(this).val(val);
        });

        $('#basic-auth-remove-btn').on('click', function () {
            let timeout = $('#basic-auth-remove-minutes').val();
            if (!timeout) {
                return;
            }
            window.location.href = '/shared-envs/remove-auth?id=<?= $model->id;?>&timeout=' + timeout;
        });
    });
</script>
