$(document).ready(function () {
    $('.link-field').each(function () {
        let $select = $(this);
        let linkTo = $select.data('link-to'); // misalnya: Customer

        $select.select2({
            placeholder: 'Select ' + linkTo,
            ajax: {
                url: linkTo.toLowerCase() + '/getLinkOptions',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                }
            }
        });
    });

    $('.numeric').keypress(function (e) {
        var key = String.fromCharCode(e.which);
        if (!(/[0-9]/.test(key))) {
            e.preventDefault();
        }
    });

    $('#new-button').click(function (e) {
        $('form').trigger('submit');
    });

    $('form').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append("_token", $(this).attr('content'));
        // console.log(formData);
        const model = $(this).attr("model");
        $.ajax({
            type: "POST",
            url: "/" + model + "/store",
            data: formData,
            dataType: "JSON",
            processData: false,   // penting
            contentType: false,
        }).done(function (res) {
            console.log(res);

        }).fail(function (res) {

        });
    });
});