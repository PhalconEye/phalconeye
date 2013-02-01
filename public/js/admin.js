admin = {};

// Support for AJAX loaded modal window.
// Focuses on first input textbox after it loads the window.
admin.modal = {
    init:function(elements){
        $(elements).click(function (e) {
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $(url).modal('open');
            } else {
                $.get(url,function (data) {
                    $(data).modal();
                    setTimeout(function () {
                        $('#modal').on('hidden', function () {
                            $(this).remove();
                        })
                    }, 500);

                }).success(function () {
                        $('input:text:visible:first').focus();
                    });
            }

            return false;
        });
    }
};

$(document).ready(function () {
    $('.button-loading')
        .click(function () {
            var btn = $(this);
            btn.button('loading');
            setTimeout(function () {
                btn.button('reset')
            }, 3000)
        });

    admin.modal.init('[data-toggle="modal"]');
});