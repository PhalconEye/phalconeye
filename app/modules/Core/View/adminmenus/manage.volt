{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Manage menu"|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        window.onload = function () {
            $("#items").sortable({
                update:function (event, ui) {
                    var order = [];
                    var index = 0;
                    ui.item.parent().children().each(function () {
                        order[index++] = $(this).attr('element_id');
                    });

                    $.ajax({
                        type:"POST",
                        url:'{{ url(['for':'admin-menus-order'])}}',
                        data:{
                            'order':order
                        },
                        dataType:'json',
                        success:function () {
                            $('#label-saved').show();
                            $('#label-saved').fadeOut(1000);
                        }
                    });
                }
            });
            $("#items").disableSelection();
        };

        var defaultItem = function () {
            return $('#default_item').html();
        }

        var addItem = function (id, label) {
            $('#items').append(defaultItem().replace(/element-id/gi, id).replace('element-label', label));
        }

        var requestAddItem = function () {
            var url = '{{ url(['for':'admin-menus-create-item'])}}';
            var data = {
                'menu_id': {{ menu.id }}
                {% if parent is defined %}, 'parent_id': {{ parent.id }}
                {% endif %}
            };

            PE.modal.open(url, data);
        }

        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this menu item?" | trans }}')) {
            {% if parent is defined %}
                window.location.href = '{{ url(['for':'admin-menus-delete-item'])}}' + id + '?parent_id={{ parent.id }}';
            {% else %}
                window.location.href = '{{ url(['for':'admin-menus-delete-item'])}}' + id;
            {% endif %}

            }
        }

        var editItem = function (id) {
            var url = '{{ url(['for':'admin-menus-edit-item'])}}' + id;
            var data = {
                'menu_id': {{ menu.id }}
                {% if parent is defined %}, 'parent_id': {{ parent.id }}
                {% endif %}
            };

            PE.modal.open(url, data);
        }

        var manageItem = function (id) {
            window.location.href = window.location.pathname + '?parent_id=' + id;
        }

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
            <h3><a href="{{ url("admin/menus")}}">{{ "Menus" | trans }}</a> >
                {% if parent is defined %}
                    {% for p in parents %}
                        <a href="{{ url(['for':'admin-menus-manage'])}}{{ menu.id }}{% if p.parent_id is not null %}?parent_id={{ p.parent_id }}{% endif %}"
                           class='btn'>{{ p.title }}</a>
                        >
                    {% endfor %}
                {% endif %}
                {{ "Manage menu" | trans }}
                "{{ menu.name }}"</h3>
            <button class="btn btn-primary" onclick='requestAddItem();'>{{ 'Add new item'|trans }}</button>
            <div id="label-saved" class="label label-success">{{ 'Saved...'|trans }}</div>
        </div>
        <div class="menu_manage_body">
            <ul id="items">
                {% for item in items %}
                    <li element_id="{{ item.id }}">
                        <div class="item_title"><i class="icon-move"></i>{{ item.title }}
                            | {{ 'Items: '|trans }}{{ item.getMenuItems().count() }}</div>
                        <div class="item_options">
                            <a class="btn btn-success" href="javascript:;"
                               onclick="manageItem({{ item.id }});">{{ 'Manage'|trans }}</a>
                            <a class="btn btn-success" href="javascript:;"
                               onclick="editItem({{ item.id }});">{{ 'Edit'|trans }}</a>
                            <a class="btn btn-success" href="javascript:;"
                               onclick="deleteItem({{ item.id }});">{{ 'Remove'|trans }}</a>
                        </div>
                    </li>
                {% endfor %}
            </ul>
        </div>
    </div>

    <div id="default_item" style="display:none;">
        <li element_id="element-id">
            <div class="item_title"><i class="icon-move"></i>element-label</div>
            <div class="item_options">
                <a class="btn btn-success" href="javascript:;"
                   onclick="manageItem(element-id);">{{ 'Manage'|trans }}</a>
                <a class="btn btn-success" href="javascript:;" onclick="editItem(element-id);">{{ 'Edit'|trans }}</a>
                <a class="btn btn-success" href="javascript:;"
                   onclick="deleteItem(element-id);">{{ 'Remove'|trans }}</a>
            </div>
        </li>
    </div>


{% endblock %}
