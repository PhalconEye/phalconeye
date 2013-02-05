// Support for AJAX loaded modal window.
modal = {
    init:function (elements) {
        $(elements).unbind('click');
        $(elements).click(function (e) {
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $(url).modal('open');
            } else {
                modal.open(url, {});
            }

            return false;
        });
    },

    open:function(url, data){
        $.get(url, data)
            .done(function (html) {
            $('<div id="modal" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">' + html + '</div>').modal({
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
                            .done(function (postHTML) {
                                $('#modal').html(postHTML);
                            });
                    }
                    else {
                        $('#modal').modal('hide');
                    }
                });
            }, 500);

        });
    }


};
