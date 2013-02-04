admin = {};

// Support for AJAX loaded modal window.
admin.modal = {
    init:function (elements) {
        $(elements).unbind('click');
        $(elements).click(function (e) {
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $(url).modal('open');
            } else {
                $.get(url,function (data) {
                    $('<div id="modal" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">' + data + '</div>').modal({
                        keyboard:false
                    });
                    setTimeout(function () {
                        // set removing
                        $('#modal').on('hidden', function () {
                            $(this).remove();
                        });

                        // prevent background from simple closing
                        $('.modal-backdrop').unbind('click');
                        $('.modal-backdrop').click(function (e) {
                            e.preventDefault();
                            if (confirm('Close this window?')) {
                                $('#modal').modal('hide');
                            }
                            return false;
                        });

                        // set submiting
                        $('#modal .btn-save').click(function () {
                            if ($('#modal form').length == 1) {
                                $.post($('#modal form').attr('action'), $('#modal form').serialize())
                                    .done(function (data) {
                                        $('#modal').html(data);
                                    });
                            }
                            else {
                                $('#modal').modal('hide');
                            }
                        });
                    }, 500);

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
            }, 1000)
        });

    admin.modal.init('[data-toggle="modal"]');
});