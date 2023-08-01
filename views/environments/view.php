<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\UserEnvironments;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\helpers\LogHelper;
use yii\helpers\ArrayHelper;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\UserEnvironments */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => "Environments", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
\app\assets\AppAssetRefreshBranches::register($this);

$updateOneBranchButtons = [];
?>
<div class="user-view">

    <h1><?= Html::encode("{$model->project->name} env") ?></h1>

    <p>
        <?php if ($model->canBeUpdated()):?>
        <?= Html::button('Update', ['class' => 'btn btn-primary', 'type' => 'button', 'id' => 'update']) ?>
        <?php endif;?>
        <?php if ($model->canBeDeleted()):?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>

    <div id="repo-branches" style="display: none">
        <?php $form = ActiveForm::begin(['id' => 'create-form', 'validateOnSubmit' => false, 'action' => [Url::toRoute(['update', 'id' => $model->id])]]); ?>
        <?php foreach ($model->branches as $branchData): ?>
            <?php $repositoryCode = $branchData->repository->code;?>
            <div class="form-group form-group-custom field-<?= $repositoryCode; ?>_repository">
                <label class="control-label" for="<?= $repositoryCode; ?>_repository">Ветка для <?= $repositoryCode; ?></label>
                <input class="form-control" name="UserEnvironments[branchesData][<?= $repositoryCode; ?>]"
                       data-provider="<?= $branchData->repository->version_control_provider; ?>" data-repository="<?= $repositoryCode; ?>" onfocus="this.select()"
                       list="<?= $repositoryCode; ?>_branches" value="<?= $branchData->branch; ?>">
                <datalist id="<?= $repositoryCode; ?>_branches">
                    <option value="<?= $branchData->branch; ?>"></option>
                </datalist>
                <button type="button" id="<?= $repositoryCode; ?>_refresh_branch_btn" class="btn btn-success btn-refresh-custom"
                        data-repo="<?= $repositoryCode; ?>">Find
                </button>
            </div>
        <?php $updateOneBranchButtons[] = Html::button("Update {$branchData->repository->code}", ['id' => $branchData->repository->code, 'class' => 'btn btn-success one-branch-update']);?>
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
                'value' => function(UserEnvironments $env){
                    return $env->project->name;
                },
            ],
            [
                'format' => 'raw',
                'label' => 'URL-s',
                'value' => function(UserEnvironments $env){
                    $code = $env->environment_code;
                    if (!$code) {
                        return null;
                    }
                    $result = [];
                    $domain = \Yii::$app->params['stageDomain'];
                    foreach (\Yii::$app->params['stageSubdomainPrefixes'][$env->project->type] as $name => $prefix) {
                        $url = "https://{$code}{$prefix}.{$domain}";
                        $name = sprintf('%s (%s)', $name, $url);
                        $result[] = "<a href='{$url}' target='_blank'>{$name}</a>";
                    }

                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Connection string',
                'value' => function(UserEnvironments $env){
                    $code = $env->environment_code;
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
                'value' => function(UserEnvironments $env){
                    $statuses = UserEnvironments::getStatuses();
                    $statusClass = UserEnvironments::getStatusClass($env->status);
                    return "<span class='label label-{$statusClass}'>{$statuses[$env->status]}</span>" ?? 'n\a';
                },
            ],
            [
                'format' => 'html',
                'label' => 'Branches',
                'value' => function(UserEnvironments $env){
                    $result = [];
                    foreach ($env->branches as $branch) {
                        $result[] = "{$branch->repository->name}: <code>{$branch->branch}</code>";
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Created At',
                'value' => function(UserEnvironments $env){
                    return date('Y-m-d H:i:s', strtotime($env->created_at));
                },
            ],
            [
                'format' => 'html',
                'label' => 'Updated At',
                'value' => function(UserEnvironments $env){
                    return date('Y-m-d H:i:s', strtotime($env->updated_at));
                },
            ],
            [
                'format' => 'html',
                'label' => 'Comment',
                'value' => function(UserEnvironments $env){
                    return $env->comment;
                },
            ],
            [
                'format' => 'html',
                'label' => 'Related services',
                'value' => function(UserEnvironments $env){
                    $result = [];
                    foreach ($env->relatedServices as $serviceEnv) {
                        $domain = \Yii::$app->params['stageDomain'];
                        $url = "https://{$serviceEnv->environment_code}.{$domain}";
                        $result[] = Html::a($serviceEnv->project->name . " ({$url})", $url, ['target' => '_blank']);
                    }
                    return implode('<br>', $result);
                },
            ],
            [
                'format' => 'html',
                'label' => 'Added users',
                'value' => function(UserEnvironments $env){
                    $result = [];
                    foreach ($env->addedUsers as $user) {
                        $result[] = $user->username;
                    }
                    return implode('<br>', $result);
                },
            ],
        ],
    ]) ?>

    <p>
        <?php
        if ($model->isInProgress()): ?>
            <?= Html::a('Status', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        endif; ?>

        <?php
        if ($model->isReady()): ?>
        <?php
        $form = ActiveForm::begin(['id' => 'create-form', 'validateOnSubmit' => false, 'action' => [Url::toRoute(['environments/add-key', 'envId' => $model->id])]]); ?>

        <?= $form->field($model, 'added_users_keys')->dropDownList(
            ArrayHelper::map(User::find()->where("coalesce(ssh_key, '') <> ''")->andWhere(['not in', 'id', $model->getAddedUsersKeys()])->orderBy('username')->all(), 'id', 'username'),
            ['multiple' => true]
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
    if ($model->isError() && ($logData = LogHelper::getLogData($model)) && $logData->isLogExist()): ?>
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
    document.addEventListener('DOMContentLoaded', function(){
        $('#update').on('click', function (){
            $('#repo-branches').slideToggle();
        });
        $('.btn-refresh-custom').on('click', function () {
            let btn = $(this);
            refreshBranches(btn, '<?= \yii\helpers\Url::toRoute(['branches']);?>');
        });

        $('.one-branch-update').on('click', function () {
            let repoCode = $(this).attr('id');
            let branch = $('*[data-repository="'+repoCode+'"]').val();

            window.location.href = '/environments/update-one?id=<?= $model->id;?>&repositoryCode='+repoCode+'&branchName='+branch;
        })
    });
</script>
