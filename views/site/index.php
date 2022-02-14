<?php

/* @var $this yii\web\View */
/* @var $projects array */

$this->title = 'My Yii Application';
?>
<div class="popup-loader">
    <div class="popup-loader-in">
        <img src="/img/loader.gif" alt="">
        <span>Подождите...</span>
    </div>
</div>
<div class="site-index">

    <div class="body-content">
        <input id="selected-project" type="hidden">
        <select id="projects" class="projects">
            <option value="0" selected="selected">Выбери проект</option>
            <?php foreach ($projects as $project):?>
                <option value="<?= $project;?>"><?= ucfirst($project);?></option>
            <?php endforeach;?>
        </select>
        <input id="apply-project-button" type="button" value="Применить" onclick="checkBranch($('.projects option:selected').val())">
        <div class="branch-status" style="display:none">
            <div class="info"></div>
            <input id="update-current" type="button" value="Обновить текущую ветку" onclick="updateCurrent()">
            <input id="check-available" type="button" value="Посмотреть доступные ветки" onclick="checkAvailable()">
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
</div>



