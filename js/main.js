$(document).ready(function () {
    init();
});

function init() {
    $.when(sendRequest({'action': 'init'})).then(function (response) {
        var projects = checkResponse(response);
        if (typeof projects !== 'undefined'){
            appendOptions(projects, 'projects');
        }
    });
}

function checkBranch(projectName) {
    if (projectName === '0') {
        alert('Выбери проект!');
    }
    else {
        $.when(sendRequest({'action': 'checkBranch', 'project-name': projectName})).then(function (response) {
            var result = checkResponse(response);
            $('.branch-status').fadeIn(500).find('.info').html(result);
            $('#projects, #apply-project-button').attr('disabled', true);
            $('#selected-project').attr('data-project-name', projectName);
        });
    }
}

function updateCurrent() {
    var projectName = getSelectedProjectName();
    $.when(sendRequest({'action': 'updateCurrent', 'project-name': projectName})).then(function (response) {
        var result = checkResponse(response);
        $('.branch-status').find('input').attr('disabled', true);
        $('.update-result').fadeIn(500).html(result);
    });
}

function checkAvailable() {
    var projectName = getSelectedProjectName();
    $.when(sendRequest({'action': 'checkAvailable', 'project-name': projectName})).then(function (response) {
        var result = checkResponse(response);
        appendOptions(result, 'branches');
        $('.check-result').fadeIn(500);
    });
}

function checkoutBranch() {
    var projectName = getSelectedProjectName();
    var selectedBranch = $('#branches option:selected').val();
    if (selectedBranch === '0'){
        alert('Выбери ветку!');
    }
    else {
        $.when(sendRequest({'action': 'checkoutBranch', 'project-name': projectName, 'branch-name': selectedBranch})).then(function (response) {
            var result = checkResponse(response);
            $('.checked-info').fadeIn(500).html(result);
        });
    }
}

function sendRequest(data) {
    return $.ajax({
        url: "/www/index.php",
        method: "POST",
        dataType: "json",
        data: data,
        error: function () {
            alert('Ошибка сервера. Попробуй позже');
        }
    });
}

function checkResponse(response) {
    if (response.status !== 'ok'){
        alert(response.data);
        throw new DOMException('error');
    }
    else {
        return response.data;
    }
}

function appendOptions(optionsData, selectId) {
    for (var i = 0; i < optionsData.length; i++) {
        $('#'+selectId).append('<option value="'+optionsData[i]+'" >'+optionsData[i]+'</option>');
    }
}

function getSelectedProjectName() {
    var name = $('#selected-project').data('project-name');
    if (typeof name === 'undefined'){
        alert('No project was selected');
    }

    return name;
}