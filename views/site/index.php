<?php

use yii\web\View;
use app\models\forms\SwitchForm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this View */

/* @var $projects array */
/* @var $model SwitchForm */

$this->title = 'Branch Switcher';
$stageAliases = Yii::$app->user->identity->getAvailableAliases();
$multiAlias = count($stageAliases) > 1;
?>
<div class="popup-loader">
    <div class="popup-loader-in">
        <img src="/img/stopwar.png" alt="">
        <span>Подождите...</span>
    </div>
</div>
<div class="site-index">

    <div class="body-content">

        <input id="selected-project" type="hidden">
        <?php
        $form = ActiveForm::begin(
            [
                'id' => 'login-form',
                'options' => ['class' => 'form-horizontal'],
            ]
        )
        ?>
        <div style="display: flex">
            <div style="width:100%;">
                <?php
                if ($multiAlias): ?>
                    <?= $form->field($model, 'alias', ['options' => ['id' => 'alias-group']])->dropDownList(
                        $stageAliases,
                        [
                            'style' => 'width:150px',
                            'id' => 'alias',
                            'prompt' => 'Выбрать',
                        ]
                    ); ?>
                <?php
                endif; ?>
                <div id="project-container" <?= $multiAlias ? 'style="display:none"' : null; ?>>
                    <h3 id="alias-title">
                        Твой алиас проектов: <b><?= !$multiAlias ? $stageAliases[0] : null; ?></b>
                    </h3>
                    <input id="current-alias" type="hidden" value="<?= $stageAliases[0]; ?>">
                    <?= $form->field($model, 'project', ['options' => ['style' => 'margin:0']])->dropDownList(
                        array_combine($projects, $projects),
                        [
                            'style' => 'width:150px',
                            'id' => 'projects',
                            'prompt' => 'Выбрать',
                        ]
                    ) ?>
                    <?= Html::button(
                        'Применить',
                        [
                            'class' => 'btn btn-primary',
                            'id' => 'apply-project-button',
                            'onclick' => "checkBranch($('#projects option:selected').val())"
                        ]
                    ) ?>
                </div>
                <?php
                ActiveForm::end() ?>

                <div class="branch-status" style="display:none">
                    <div class="info"></div>
                    <input id="update-current" type="button" value="Обновить текущую ветку" onclick="updateCurrent()">
                    <input id="check-available" type="button" value="Посмотреть доступные ветки"
                           onclick="checkAvailable()">
                </div>
                <div class="result">
                    <div class="update-result" style="display: none"></div>
                    <div class="check-result" style="display: none">
                        <input list="branches" id="selected-branch">
                        <datalist id="branches">
                        </datalist>
                        <input id="checkout" type="button" value="Переключить" onclick="checkoutBranch()">
                    </div>
                </div>
            </div>
            <div id="info" class="container">

            </div>

        </div>
        <div class="checked-info"></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let stageId = '';
        <?php if ($multiAlias): ?>
        $('#alias').change(function (e) {
            let alias = $(this).find(":selected").text().toLowerCase();
            $('#current-alias').val(alias);
            $('#alias-group').remove();
            $('#alias-title').append('<b>'+alias+'</b>').show();
            $('#project-container').show();
            stageId = alias;
        });
        <?php else: ?>
        stageId = '<?= $stageAliases[0];?>';
        <?php endif; ?>

        $('#projects').change(function (e) {
            setProjectLinks(stageId);
        });
    })

    function setProjectLinks(stageId) {
        let mapping = {};
        mapping.king = 'slotoking';
        mapping.vlk = '777originals';
        mapping.ng = 'vipnetgame';
        mapping.reel = 'reelemperror';
        mapping.lavina = 'lavina';
        mapping.funrize = 'funrize';
        mapping.nlc = 'nolimitcoins';
        mapping.tao = 'taofortune';

        let project = $('#projects').find(":selected").text().toLowerCase().replace('-backend', '');
        if (!(project in mapping)){
            return;
        }

        let domain = '<?= Yii::$app->params['stageDomain'] ?? ''?>';
        let url, adminUrl;
        if (stageId === 'main') {
            url = 'https://' + mapping[project] + '.' + domain;
            adminUrl = 'https://admin-' + mapping[project] + '.' + domain;
        } else {
            url = 'https://' + project + '-' + stageId + '.' + domain;
            adminUrl = 'https://admin-' + project + '-' + stageId + '.' + domain;
        }
        let frontLink = document.createElement('a');
        let adminLink = document.createElement('a');
        frontLink.text = url;
        frontLink.href = url;
        frontLink.target = '_blank';
        adminLink.text = adminUrl;
        adminLink.href = adminUrl;
        adminLink.target = '_blank';
        $('#info').html('').append(frontLink).append('<br>').append(adminLink);
    }

</script>



