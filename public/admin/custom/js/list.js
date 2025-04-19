$(document).ready(function () {
    $('#check-all').change(function (e) {
        e.preventDefault();
        checkAll($(this).is(':checked'));
    });

    $('.check-data').change(function (e) {
        e.preventDefault();
        showHideButton();
    });
});

function checkAll(checked) {
    $('.check-data').prop('checked', checked);
    showHideButton();
}

function showHideButton() {
    const $actionButton = $('#action-button');
    const $newButton = $('#new-button');
    const $checkAll = $('#check-all');

    if ($('.check-data:checked').length > 0) {
        $actionButton.removeClass('d-none');
        $newButton.addClass('d-none');
    } else {
        $actionButton.addClass('d-none');
        $newButton.removeClass('d-none');
        $checkAll.prop('checked', false);
    }
}