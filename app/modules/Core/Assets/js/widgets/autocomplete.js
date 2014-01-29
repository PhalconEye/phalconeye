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
 * Autocomplete initializer.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    $(function () {
        root.ns(
            'PhalconEye.widget.autocomplete',
            {
                /**
                 * Init autocomplete.
                 *
                 * @param element Element object.
                 */
                init: function (element) {
                    var result = element.autocomplete({
                        source: function (request, response) {
                            $.ajax({
                                url: $(this)[0].element[0].dataset.link,
                                type: 'get',
                                data: {query: request.term},
                                dataType: 'json',
                                success: function (json) {
                                    response($.map(json, function (item) {
                                        return {
                                            label: item.label,
                                            value: item.id
                                        }
                                    }));
                                }
                            });
                        },
                        select: function (event, ui) {
                            $(event.target).val(ui.item.label);

                            var targetElement = $(event.target)[0].dataset.target;

                            if (targetElement) {
                                $(targetElement).val(ui.item.value);
                            }
                            return false;
                        }
                    }).data("autocomplete");

                    if (result) {
                        result._resizeMenu = function () { // fix position of dropdown
                            var ul = this.menu.element;
                            ul.outerWidth(this.element.outerWidth());
                        }
                    }
                }
            }
        );
    });
}(window, jQuery, PhalconEye));

