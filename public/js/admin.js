admin = {};

$(document).ready(function () {
    $('.button-loading')
        .click(function () {
            var btn = $(this);
            btn.button('loading');
            setTimeout(function () {
                btn.button('reset')
            }, 1000)
        });

    modal.init('[data-toggle="modal"]');
});