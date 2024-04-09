<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Project;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\models\UserEnvironments;

/* @var $this View */
/* @var $model UserEnvironments */
/* @var $form ActiveForm */
/* @var $availableProjects Project[] */

\app\assets\AppAssetRefreshBranches::register($this);
?>

<div class="user-environments-form">

    <?php $form = ActiveForm::begin(['id' => 'create-form', 'validateOnSubmit' => false]); ?>

    <?= $form->field($model, 'project_id')->dropDownList(ArrayHelper::map($availableProjects, 'id', 'name'), ['id' => 'project', 'prompt'=>'Выбрать проект'] ) ?>

    <div id="repo-branches">

    </div>

    <?= $form->field($model, 'comment')->textarea();?>

    <?php if ($availableServices = UserEnvironments::findAvailableServicesForRelate(Yii::$app->user->id)):?>
    <?= $form->field($model, 'related_services_id')->dropDownList(ArrayHelper::map($availableServices, 'id', function (UserEnvironments $env) {
            return $env->project->name . " ($env->environment_code)";
        }), ['multiple' => true]); ?>
    <?php endif;?>

    <?= $form->field($model, 'is_run_autotest')->checkbox();?>

    <div class="form-group">
        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        $('#create-form').on('submit', function (e) {
            if ($(this).data('submitted')) {
                e.preventDefault();
            }

            $(this).data('submitted', true);
        });
        $('#project').change(function (){
            let projId = $(this).find(":selected").val();
            let url = '<?= \yii\helpers\Url::to('repositories');?>?projectId=' + projId;
            $.ajax({
                url: url,
                success: function (data) {
                    if (data.success) {
                        let branchesSelectors = $('#repo-branches');
                        branchesSelectors.empty();
                        for (const [key, value] of Object.entries(data.repositoriesData)) {
                            const select = createSelect(key, value);
                            branchesSelectors.append(select);
                        }
                        $('.btn-refresh-custom').on('click', function () {
                            let btn = $(this);
                            refreshBranches(btn, '<?= \yii\helpers\Url::to('branches');?>');
                        })
                    } else {
                        alert(data.message);
                    }
                }
            });
        });
    });

    function createSelect(name, options) {
        const selectId = name + '_repository';
        const div = document.createElement('div');
        div.className = 'form-group form-group-custom field-' + selectId;
        const label = document.createElement('label');
        label.className = 'control-label';
        label.setAttribute('for', selectId);
        label.textContent = 'Ветка для ' + name;


        const datalist = document.createElement("datalist");
        datalist.id = name + '_branches';
        const input = document.createElement("input");
        input.className = 'form-control';
        input.name = `UserEnvironments[branchesData][${name}]`;
        input.setAttribute('data-provider', options.provider);
        input.setAttribute('data-repository', name);
        input.setAttribute('onFocus', 'this.select()');
        input.setAttribute('list', name + '_branches');
        input.value = options.defaultBranch;
        input.appendChild(datalist);

        const refreshBranchesBtn = document.createElement("button");
        refreshBranchesBtn.type = 'button';
        refreshBranchesBtn.id = name + '_refresh_branch_btn';
        refreshBranchesBtn.className = 'btn btn-success btn-refresh-custom';
        refreshBranchesBtn.textContent = 'Find';
        refreshBranchesBtn.setAttribute('data-repo', name);

        div.append(label);
        div.append(input);
        div.append(datalist);
        div.append(refreshBranchesBtn);

        return div;
    }
</script>
