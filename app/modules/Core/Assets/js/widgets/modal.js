/*
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconeye.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
 +------------------------------------------------------------------------+
 */

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
                        var modalObject = $('<div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true"> <div class="modal-dialog"><div class="modal-content">' + html + '</div></div></div>').filter('.modal');
                        modalObject.modal({
                            backdrop: 'static',
                            keyboard: false
                        });

                        modalObject.on('shown.bs.modal', function () {
                            PhalconEye.core.hideLoadingStage();

                            // Set removing.
                            modalObject.on('hidden.bs.modal', function () {
                                $(this).remove();
                            });

                            PhalconEye.widget.modal.bindSubmit(modalObject);

                            // Evaluate js
                            $(html).filter("script").each(function () {
                                var scriptContent = $(this).html(); //Grab the content of this tag
                                eval(scriptContent); //Execute the content
                            });
                        })
                    });
            },

            hide: function () {
                if ($('#modal')) {
                    $('#modal').modal('hide');
                }
            },

            /**
             * Bind some submit events for modal form.
             */
            bindSubmit: function (modalObject) {
                var form = $('form', modalObject),
                    saveButton = $('.btn-save', modalObject);

                // Set submitting.
                saveButton.click(function () {
                    if (form.length == 1) {
                        PhalconEye.core.showLoadingStage();

                        // Check ckeditor.
                        if (Object.keys(CKEDITOR.instances).length > 0) {
                            for (var instance in CKEDITOR.instances) {
                                var elementId = '#' + CKEDITOR.instances[instance].name;
                                $(elementId).val(CKEDITOR.instances[instance].getData());
                            }
                        }

                        $.post(form.attr('action'), form.serialize())
                            .done(function (postHTML) {
                                $('.modal-content', modalObject).html(postHTML);
                                PhalconEye.widget.modal.bindSubmit(modalObject);
                                PhalconEye.core.hideLoadingStage();
                            });
                    }
                    else {
                        modalObject.modal('hide');
                    }
                });

                form.submit(function () {
                    PhalconEye.core.showLoadingStage();
                    saveButton.click();
                    return false;
                });

                modalObject.on('hidden.bs.modal', function () {
                    PhalconEye.core.hideLoadingStage();
                });

                // Ckeditor save button.
                setTimeout((function () {
                    if (Object.keys(CKEDITOR.instances).length > 0) {
                        for (var instance in CKEDITOR.instances) {
                            if (CKEDITOR.instances[instance].commands.save) {
                                CKEDITOR.instances[instance].commands.save.exec = function () {
                                    saveButton.click();
                                }
                            }
                        }
                    }
                }), 1000);
            }
        }
    );
}(window, jQuery, PhalconEye));
