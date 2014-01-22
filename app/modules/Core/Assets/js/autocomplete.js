/**
 * Autocomplete initializer.
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
        root.autocomplete = {
            init: function () {
                var result = $('[data-autocomplete]').autocomplete({
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
                    open: function () {
                        $(this).data("autocomplete").menu.element.attr('class', "typeahead dropdown-menu");
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
        };

        root.autocomplete.init();
    });
}(window, jQuery, PhalconEye));

