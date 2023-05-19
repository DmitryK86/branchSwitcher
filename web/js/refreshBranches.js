function refreshBranches(btn, route) {
    if (btn.prop('disabled') === true) {
        return;
    }
    btn.prop('disabled', true);
    const repoCode = btn.data('repo');
    const input = $(`*[data-repository="${repoCode}"]`);
    let url = route + '?code=' + repoCode + '&searchBranch=' + input.val();
    $.ajax({
        url: url,
        success: function (data) {
            if (data.success) {
                let datalist = $(`#${repoCode}_branches`);
                datalist.empty();
                let options = data.branches;
                for (let i = 0; i < options.length; i++) {
                    datalist.append('<option value="'+options[i]+'" >');
                }
                input.focus();
            } else {
                alert(data.message);
            }
        },
        complete: function () {
            btn.prop('disabled', false);
        }
    });
}