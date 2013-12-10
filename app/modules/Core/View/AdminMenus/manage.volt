{#
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconeye.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Manage menu"|trans }}{% endblock %}

{% block header %}
    <div class="navbar navbar-header">
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
    </div>
{% endblock %}

{% block head %}
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

        {{ helper('core').JsTrans('Are you really want to delete this menu item?') }}

    </script>

{% endblock %}

{% block content %}

    <div class="span12">
        <div class="menu_manage_header">
            <h3><a href="{{ url("admin/menus") }}">{{ "Menus" | trans }}</a> >
                {% if parent is defined %}
                    {% for p in parents %}
                        <a href="{{ url(['for':'admin-menus-manage']) }}{{ menu.id }}{% if p.parent_id is not null %}?parent_id={{ p.parent_id }}{% endif %}"
                           class='btn'>{{ p.title }}</a>
                        >
                    {% endfor %}
                {% endif %}
                {{ "Manage menu" | trans }}
                "{{ menu.name }}"</h3>
            <button id="add-new-item" class="btn btn-primary">{{ 'Add new item'|trans }}</button>
            <div id="label-saved" class="label label-success">{{ 'Saved...'|trans }}</div>
        </div>
        <div class="menu_manage_body">
            <ul id="items">
                {% for item in items %}
                    <li data-item-id="{{ item.id }}">
                        <div class="item_title">
                            <i class="icon-move"></i>
                            {{ item.title }}
                            | {{ 'Items: '|trans }}{{ item.getMenuItems() ? item.getMenuItems().count() : 0 }}
                        </div>
                        <div class="item_options">
                            <a class="btn btn-success item-manage" href="javascript:;">{{ 'Manage'|trans }}</a>
                            <a class="btn btn-success item-edit" href="javascript:;">{{ 'Edit'|trans }}</a>
                            <a class="btn btn-success item-delete" href="javascript:;">{{ 'Remove'|trans }}</a>
                        </div>
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>

    {#ITEM TEMPLATE#}
    <div id="default_item" style="display:none;">
        <li data-item-id="element-id">
            <div class="item_title"><i class="icon-move"></i>element-label</div>
            <div class="item_options">
                <a class="btn btn-success item-manage" href="javascript:;">{{ 'Manage'|trans }}</a>
                <a class="btn btn-success item-edit" href="javascript:;">{{ 'Edit'|trans }}</a>
                <a class="btn btn-success item-delete" href="javascript:;">{{ 'Remove'|trans }}</a>
            </div>
        </li>
    </div>
    {# END OF ITEM TEMPLATE#}

{% endblock %}
