/**
 * Modal forms.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

(function (window, $, root, undefined) {
    $(function () {
        root.modal = {
            init: function (elements) {
                $(elements).unbind('click');
                $(elements).click(function (e) {
                    var url = $(this).attr('href');
                    if (url.indexOf('#') == 0) {
                        $(url).modal('open');
                    } else {
                        root.modal.open(url, {});
                    }

                    return false;
                });
            },

            open: function (url, data) {
                this.showLoadingStage();
                $.get(url, data)
                    .done(function (html) {
                        root.modal.hideLoadingStage();
                        var modalTemplate = $('<div id="modal" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">' + html + '</div>').filter('.modal');
                        modalTemplate.modal({
                            keyboard: false
                        });

                        modalTemplate.on('focus', function (e) { // focus bug workaround
                            e.preventDefault();
                        });

                        modalTemplate.on('shown', function () {
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


                            root.modal._bindSubmit();
                            root.autocomplete.init();

                            // Evaluate js
                            $(html).filter("script").each(function () {
                                var scriptContent = $(this).html(); //Grab the content of this tag
                                eval(scriptContent); //Execute the content
                            });
                        })

                    });
            },

            showLoadingStage: function () {
                var bg = $('<div id="modal_loading" class="modal_loading"><span></span></div>');
                $(window.document.body).append(bg);
            },

            hideLoadingStage: function () {
                if ($('#modal_loading')) {
                    $('#modal_loading').remove();
                }
            },

            _bindSubmit: function () {
                // set submiting
                $('#modal .btn-save').click(function () {
                    if ($('#modal form').length == 1) {
                        // check ckeditor
                        if (Object.keys(CKEDITOR.instances).length > 0) {
                            for (var instance in CKEDITOR.instances) {
                                var elementId = '#' + CKEDITOR.instances[instance].name;
                                $(elementId).val(CKEDITOR.instances[instance].getData());
                            }
                        }


                        $.post($('#modal form').attr('action'), $('#modal form').serialize())
                            .done(function (postHTML) {
                                $('#modal').html(postHTML);
                                root.modal._bindSubmit();
                                root.autocomplete.init();
                            });
                    }
                    else {
                        $('#modal').modal('hide');
                    }
                });

                $('#modal form').submit(function () {
                    $('#modal .btn-save').click();
                    return false;
                });

                // chkeditor save button
                setTimeout((function () {
                    if (Object.keys(CKEDITOR.instances).length > 0) {
                        for (var instance in CKEDITOR.instances) {
                            if (CKEDITOR.instances[instance].commands.save) {
                                CKEDITOR.instances[instance].commands.save.exec = function () {
                                    $('#modal .btn-save').click();
                                }
                            }
                        }
                    }
                }), 1000);
            }
        };

        root.modal.init('[data-toggle="modal"]');
    });
}(window, jQuery, PE));
