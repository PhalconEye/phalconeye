/**
 * Widgets logic.
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
        setup();
        root.Widget.init();

        /**
         * Init on update.
         */
        $(document).bind("DOMNodeInserted", function (event) {
            PhalconEye.Widget.init(event.target);
        });

        function setup() {
            var widget = root.ns('PhalconEye.Widget');

            /**
             * Init widgets.
             *
             * @param context
             */
            widget.init = function (context) {
                /**
                 * Look for all widgets.
                 */
                $('[data-widget]', context).each(function () {
                    var widgets = this.getAttribute('data-widget').split(/\s?,\s?/)
                        , len = widgets.length
                        , widget
                        , data;

                    for (; len--;) {
                        data = widgets[len].split('|');
                        if (data[0].indexOf('invoked') != -1) {
                            continue;
                        }

                        if (!(data[0] in root.Widget)) {
                            PhalconEye.Helper.log('Widget with name "{name}" not found.', {name: data[0]});
                            continue;
                        }

                        widget = PhalconEye.Widget[data[0]];
                        if ('function' == typeof widget) {
                            widget.apply(this, data[1] ? data[1].split(' ') : []);
                            widgets[len] = '(' + widgets[len] + '):invoked';
                        }
                    }

                    this.setAttribute('data-widget', widgets.join(', '))
                });
            };

            /**
             * Init CkEditor element.
             */
            widget.ckeditor = function () {
                var $this = $(this);
                CKEDITOR.replace($this.data('name'), $.parseJSON($this.data('options')));
            };

        }
    });
}(window, jQuery, PhalconEye));

