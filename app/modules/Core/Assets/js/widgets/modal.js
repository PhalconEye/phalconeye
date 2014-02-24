/**
 * Modal forms.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    root.ns(
        'PhalconEye.widget.modal',
        {
            /**
             * Init ckeditor.
             *
             * @param elements Element(s) to init.
             */
            init: function (elements) {
                $(elements).unbind('click');
                $(elements).click(function (e) {
                    var url = $(this).attr('href');
                    if (url.indexOf('#') == 0) {
                        return false;
                    } else {
                        PhalconEye.widget.modal.open(url, {});
                    }

                    return false;
                });
            },

            /**
             * Open modal for url with data.
             *
             * @param url Url to open.
             * @param data With data.
             */
            open: function (url, data) {
                PhalconEye.core.showLoadingStage();
                $.get(url, data)
                    .done(function (html) {
                        PhalconEye.core.hideLoadingStage();
                        var modalTemplate = $('<div id="modal" class="modal hide fade" tabindex="1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">' + html + '</div>').filter('.modal');
                        modalTemplate.modal({
                            keyboard: false
                        });

                        modalTemplate.on('focus', function (e) { // focus bug workaround
                            e.preventDefault();
                        });

                        modalTemplate.on('shown', function () {
                            // Set removing.
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

                            PhalconEye.widget.modal.bindSubmit();

                            // Evaluate js
                            $(html).filter("script").each(function () {
                                var scriptContent = $(this).html(); //Grab the content of this tag
                                eval(scriptContent); //Execute the content
                            });
                        })

                    });
            },

            /**
             * Bind some submit events for modal form.
             */
            bindSubmit: function () {
                // Set submitting.
                $('#modal .btn-save').click(function () {
                    if ($('#modal form').length == 1) {
                        PhalconEye.core.showLoadingStage();

                        // Check ckeditor.
                        if (Object.keys(CKEDITOR.instances).length > 0) {
                            for (var instance in CKEDITOR.instances) {
                                var elementId = '#' + CKEDITOR.instances[instance].name;
                                $(elementId).val(CKEDITOR.instances[instance].getData());
                            }
                        }

                        $.post($('#modal form').attr('action'), $('#modal form').serialize())
                            .done(function (postHTML) {
                                $('#modal').html(postHTML);
                                PhalconEye.widget.modal.bindSubmit();
                            });
                    }
                    else {
                        $('#modal').modal('hide');
                    }
                });

                $('#modal form').submit(function () {
                    PhalconEye.core.showLoadingStage();
                    $('#modal .btn-save').click();
                    return false;
                });

                // Ckeditor save button.
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
        }
    );
}(window, jQuery, PhalconEye));
