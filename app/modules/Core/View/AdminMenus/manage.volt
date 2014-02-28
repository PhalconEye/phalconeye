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

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Manage menu"|i18n }}{% endblock %}

{% block head %}
    {{ helper('assets').addJs('assets/js/core/admin/menu.js') }}
    {{ helper('assets').addJs('assets/js/core/widgets/modal.js') }}
    {{ helper('assets').addJs('assets/js/core/widgets/ckeditor.js') }}

    <script type="text/javascript">
        var menuItemsData = {
            'menu_id': {{ menu.id }},
            'link_create': '{{ url(['for':'admin-menus-create-item'])}}',
            'link_edit': '{{ url(['for':'admin-menus-edit-item'])}}',
            'link_delete': '{{ url(['for':'admin-menus-delete-item'])}}',
            'link_order': '{{ url(['for':'admin-menus-order'])}}'
            {% if parent is defined %}, 'parent_id': {{ parent.id }}
            {% endif %}
        };
    </script>
{% endblock %}

{% block header %}
    <div class="navbar navbar-header">
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
    </div>
{% endblock %}

{% block content %}

    <div class="span12">
        <div class="menu_manage_header">
            <h3>
                <a href="{{ url("admin/menus") }}">{{ "Menus" |i18n }}</a> >
                {% if parent is defined %}
                    {% for p in parents %}
                        <a href="{{ url(['for':'admin-menus-manage']) }}{{ menu.id }}{% if p.parent_id is not null %}?parent_id={{ p.parent_id }}{% endif %}"
                           class='btn'>{{ p.title }}</a>
                        >
                    {% endfor %}
                {% endif %}
                {{ "Manage menu" |i18n }}
                "{{ menu.name }}"
            </h3>
            <button id="add-new-item" class="btn btn-primary">{{ 'Add new item'|i18n }}</button>
            <div id="label-saved" class="label label-success">{{ 'Saved...'|i18n }}</div>
        </div>
        <div class="menu_manage_body">
            <ul id="items">
                {% for item in items %}
                    <li data-item-id="{{ item.id }}">
                        <div class="item_title">
                            <i class="glyphicon glyphicon-move"></i>
                            {{ item.title }}
                            | {{ 'Items: '|i18n }}{{ item.getMenuItems() ? item.getMenuItems().count() : 0 }}
                        </div>
                        <div class="item_options">
                            <a class="btn btn-success item-manage" href="javascript:;">{{ 'Manage'|i18n }}</a>
                            <a class="btn btn-success item-edit" href="javascript:;">{{ 'Edit'|i18n }}</a>
                            <a class="btn btn-success item-delete" href="javascript:;">{{ 'Remove'|i18n }}</a>
                        </div>
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>

    {#ITEM TEMPLATE#}
    <div id="default_item" style="display:none;">
        <li data-item-id="element-id">
            <div class="item_title"><i class="glyphicon glyphicon-move"></i>element-label</div>
            <div class="item_options">
                <a class="btn btn-success item-manage" href="javascript:;">{{ 'Manage'|i18n }}</a>
                <a class="btn btn-success item-edit" href="javascript:;">{{ 'Edit'|i18n }}</a>
                <a class="btn btn-success item-delete" href="javascript:;">{{ 'Remove'|i18n }}</a>
            </div>
        </li>
    </div>
    {# END OF ITEM TEMPLATE#}

{% endblock %}
