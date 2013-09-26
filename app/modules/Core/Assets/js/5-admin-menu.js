/**
 * Admin menus management javascript.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, data, undefined) {
    $(function () {
        console.log(PE.i18n._('TEST'));
        console.log(PE.i18n._('TEST {0} YES {1}', [11, 22]));

        var container = $("#items");

        var defaultItem = function () {
            return $('#default_item').html();
        };

        var editAction = function () {
            var id = '';
            if ($(this).parents('li').length && $(this).parents('li').data('item-id')) {
                id = $(this).parents('li').data('item-id');
            }
            root.modal.open(data.link_edit + id, data);
        };

        var addAction = function () {
            root.modal.open(data.link_create, data);
        };

        var deleteAction = function () {
            var id = $(this).parents('li').data('item-id');
            if (confirm(root.i18n._('Are you really want to delete this menu item?'))) {
                if (data.parent_id) {
                    window.location.href = data.link_delete + id + '?parent_id=' + data.parent_id;
                }
                else {
                    window.location.href = data.link_delete + id;
                }
            }
        };

        var manageAction = function () {
            window.location.href = window.location.pathname + '?parent_id=' + $(this).parents('li').data('item-id');
        };

        container.sortable({
            update: function (event, ui) {
                var order = [];
                var index = 0;
                ui.item.parent().children().each(function () {
                    order[index++] = $(this).data('item-id');
                });

                $.ajax({
                    type: "POST",
                    url: data.link_order,
                    data: {
                        'order': order
                    },
                    dataType: 'json',
                    success: function () {
                        $('#label-saved').show();
                        $('#label-saved').fadeOut(1000);
                    }
                });
            }
        });
        container.disableSelection();

        $('#add-new-item').click(addAction);
        container.on('click', '.item-edit', editAction);
        container.on('click', '.item-delete', deleteAction);
        container.on('click', '.item-manage', manageAction);

        window.addItem = function (id, label) {
            container.append(defaultItem().replace(/element-id/gi, id).replace('element-label', label));
        };
    });
}(window, jQuery, PE, menuItemsData));

