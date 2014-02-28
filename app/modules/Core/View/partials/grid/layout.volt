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

<table id="{{ grid.getId() }}" class="table grid-table" data-widget="grid">
    <thead>
    <tr>
        {% for name, column in grid.getColumns() %}
            <th>
                {% if column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_SORTABLE')] is defined and column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_SORTABLE')] %}
                    <a href="javascript:;" class="grid-sortable" data-sort="{{ name }}" data-direction="">
                        {{ column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_LABEL')] |i18n }}
                    </a>
                {% else %}
                    {{ column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_LABEL')] |i18n }}
                {% endif %}
            </th>
        {% endfor %}
        {% if grid.hasActions() %}
            <th class="actions">{{ 'Actions' |i18n }}</th>
        {% endif %}
    </tr>
    {% if grid.hasFilterForm() %}
        <tr class="grid-filter">
            {% for column in grid.getColumns() %}
                <th>
                    {% if column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_FILTER')] is defined and instanceof(column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_FILTER')], 'Engine\Form\AbstractElement') %}
                        {% set element = column[constant('\Engine\Grid\AbstractGrid::COLUMN_PARAM_FILTER')] %}
                        {{ element.setAttribute('autocomplete', 'off').render() }}
                    {% endif %}
                    <div class="clear-filter"></div>
                </th>
            {% endfor %}
            <th class="actions">
                <button class="btn btn-filter btn-primary">{{ 'Filter' |i18n }}</button>
                <button class="btn btn-warning">{{ 'Reset' |i18n }}</button>
            </th>
        </tr>
    {% endif %}
    </thead>
    {{ partial(grid.getTableBodyView(), ['grid': grid]) }}
</table>