<?php

/* @var $this yii\web\View */
/* @var $projects array */

$this->title = 'My Yii Application';
$stageAlias = Yii::$app->user->identity->alias;
?>
<div class="popup-loader">
    <div class="popup-loader-in">
        <img src="/img/loader2.gif" alt="">
        <span>Подождите...</span>
    </div>
</div>
<div class="site-index">

    <div class="body-content">
        <h3>Твой алиас проектов: <b><?= $stageAlias;?></b></h3>
        <div style="display: flex">
            <div class="container">
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
            <div id="info" class="container">

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (){
        let domain = '<?= Yii::$app->params['stageDomain'] ?? ''?>';
        let stageId = '<?= $stageAlias?>';
        $('#projects').change(function (e){
            let project = $(this).find(":selected").text().toLowerCase().replace('-backend', '');
            let url = 'https://'+project+'-'+stageId+'.'+domain;
            let adminUrl = 'https://admin-'+project+'-'+stageId+'.'+domain;
            let frontLink = document.createElement('a');
            let adminLink = document.createElement('a');
            frontLink.text = url;
            frontLink.href = url;
            frontLink.target = '_blank';
            adminLink.text = adminUrl;
            adminLink.href = adminUrl;
            adminLink.target = '_blank';
            $('#info').html('').append(frontLink).append('<br>').append(adminLink);
        })
    })

</script>



