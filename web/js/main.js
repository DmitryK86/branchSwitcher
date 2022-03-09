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
        disableInputs('branch-status');
        $('.update-result').fadeIn(500).html(result);
    });
}

let allowedBranches;
function checkAvailable() {
    var projectName = getSelectedProjectName();
    $.when(sendRequest({'action': 'checkAvailable', 'project-name': projectName})).then(function (response) {
        allowedBranches = checkResponse(response);
        disableInputs('branch-status');
        appendOptions(allowedBranches, 'branches');
        $('.check-result').fadeIn(500);
    });
}

function checkoutBranch() {
    var projectName = getSelectedProjectName();
    var selectedBranch = $('#selected-branch').val();
    if (allowedBranches.includes(selectedBranch) === false){
        alert('Ветка указана не верно');
    }
    else {
        $.when(sendRequest({'action': 'deploy', 'project-name': projectName, 'branch-name': selectedBranch})).then(function (response) {
            var result = checkResponse(response);
            disableInputs('check-result');
            $('.checked-info').fadeIn(500).html(result);
        });
    }
}

function sendRequest(data) {
    data.alias = $('#current-alias').val();
    showPopup(true);
    return $.ajax({
        url: "/",
        method: "POST",
        dataType: "json",
        data: data,
        error: function (res) {
            alert('Ошибка сервера. Попробуй позже');
        },
        complete: function () {
            showPopup(false);
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
        $('#'+selectId).append('<option value="'+optionsData[i]+'" >');
    }
}

function getSelectedProjectName() {
    var name = $('#selected-project').data('project-name');
    if (typeof name === 'undefined'){
        alert('No project was selected');
    }

    return name;
}

function disableInputs(divWithInputsClass) {
    $('.' + divWithInputsClass).find('input').attr('disabled', true);
}

function showPopup(load) {
    var popup = $('.popup-loader');
    load ? popup.fadeIn(300) : popup.fadeOut(300);
}
