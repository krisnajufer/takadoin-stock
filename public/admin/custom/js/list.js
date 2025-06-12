$(document).ready(function () {
    $('#check-all').change(function (e) {
        e.preventDefault();
        checkAll($(this).is(':checked'));
    });

    $('.check-data').change(function (e) {
        e.preventDefault();
        showHideButton();
    });

    // let table = $('#dataTabe').DataTable({
    //     ajax: "/" + $(this).attr('model') + "/getData",
    //     column: [{
    //         data: null,
    //         render: function (data, type, row, meta) {
    //             let html = '<div class="form-check style-check d-flex align-items-center">'
    //             html += '<input class="form-check-input check-data" type="checkbox">'
    //             html += '</div>'
    //             return html
    //         }
    //     }, {
    //         data: "id"
    //     },
    //     {
    //         data: "firstname",
    //         render: function (data, type, row, meta) {
    //             return row.firstname + " " + row.lastname;
    //         }
    //     },
    //     {
    //         data: "gender"
    //     },
    //     {
    //         data: "phone"
    //     }
    //     ]
    // });

    let table = new DataTable('#dataTable', {
        // ajax: "/" + $('#dataTable').attr('model') + "/getData",
        ajax: {
            url: "/" + $('#dataTable').attr('model') + "/getData",
            dataSrc: 'data'
        },
        serverSide: true,
        processing: true,
        column: [{
            data: "id",
            render: function (data, type, row, meta) {
                let html = '<div class="form-check style-check d-flex align-items-center">'
                html += '<input class="form-check-input check-data" type="checkbox">'
                html += '</div>'
                return html
            }
        }, {
            data: "id"
        },
        {
            data: "firstname",
            render: function (data, type, row, meta) {
                return row.firstname + " " + row.lastname;
            }
        },
        {
            data: "gender"
        },
        {
            data: "phone"
        }
        ]
    })
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