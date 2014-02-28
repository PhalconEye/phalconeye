{#
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
#}

<tbody>
{% if grid.getTotalCount() %}
    {% for item in grid.getItems() %}
        <tr>
            {{ partial(grid.getItemView(), ['grid': grid, 'item': item]) }}
        </tr>
    {% endfor %}
    <tr>
        <td colspan="{{ (grid.getColumns() | length) + 1 }}">
            {{ partial(resolveView('partials/paginator', 'core')) }}
        </td>
    </tr>
{% else %}
    <tr>
        <td class="grid-no-items" colspan="{{ (grid.getColumns() | length) + 1 }}">
            {{ 'No items'|i18n }}
        </td>
    </tr>
{% endif %}
</tbody>