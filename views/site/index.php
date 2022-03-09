<?php

/* @var $this yii\web\View */

/* @var $projects array */

$this->title = 'My Yii Application';
$stageAliases = Yii::$app->user->identity->getAvailableAliases();
?>
<div class="popup-loader">
    <div class="popup-loader-in">
        <img src="/img/stopwar.png" alt="">
        <span>Подождите...</span>
    </div>
</div>
<div class="site-index">

    <div class="body-content">
        <?php
        if (count($stageAliases) == 1): ?>
            <h3>Твой алиас проектов: <b><?= $stageAliases[0]; ?></b></h3>
            <input id="current-alias" type="hidden" value="<?= $stageAliases[0]; ?>">
        <?php
        else: ?>
            <h3 id="alias-title" style="display: none">Твой алиас проектов: </b></h3>
            <input id="current-alias" type="hidden">
            <div style="display: flex">
                <div class="container">
                    <select id="aliases">
                        <option value="0" selected="selected">Выбери стейдж</option>
                        <?php
                        foreach ($stageAliases as $alias): ?>
                            <option value="<?= $alias; ?>"><?= $alias; ?></option>
                        <?php
                        endforeach; ?>
                    </select>
                </div>
            </div>
        <?php
        endif; ?>
        <div style="display: flex">
            <div id="project-container" class="container" <?= count($stageAliases) > 1 ? 'style="display: none"' : null;?>>
                <input id="selected-project" type="hidden">
                <select id="projects" class="projects">
                    <option value="0" selected="selected">Выбери проект</option>
                    <?php
                    foreach ($projects as $project): ?>
                        <option value="<?= $project; ?>"><?= ucfirst($project); ?></option>
                    <?php
                    endforeach; ?>
                </select>
                <input id="apply-project-button" type="button" value="Применить"
                       onclick="checkBranch($('.projects option:selected').val())">
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
                        <div class="checked-info" style="display: none"></div>
                    </div>
                </div>
            </div>
            <div id="info" class="container">

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let stageId = '';
        <?php if (count($stageAliases) > 1): ?>
        $('#aliases').change(function (e) {
            let alias = $(this).find(":selected").text().toLowerCase();
            $('#current-alias').val(alias);
            $(this).hide();
            $('#alias-title').append(alias).show();
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

        let domain = '<?= Yii::$app->params['stageDomain'] ?? ''?>';
        let project = $('#projects').find(":selected").text().toLowerCase().replace('-backend', '');
        let url, adminUrl;
        if (stageId === 'main'){
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



