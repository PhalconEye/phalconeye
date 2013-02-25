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
                        url:'{{ url("admin/menus/orderItem")}}',
                        data:{
                            'order':order
                        },
                        dataType:'json',
                        success:function () {
                            $('#label-saved').show();
                            $('#label-saved').fadeOut(600);
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
            var url = '{{ url("admin/menus/createItem")}}';
            var data = {
                'menu_id': {{ menu.getId() }}
                {% if parent is defined %}, 'parent_id': {{ parent.getId() }}
                {% endif %}
            };

            PE.modal.open(url, data);
        }

        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this menu item?" | trans }}')) {
            {% if parent is defined %}
                window.location.href = '{{ url("admin/menus/deleteItem/")}}' + id + '?parent_id={{ parent.getId() }}';
            {% else %}
                window.location.href = '{{ url("admin/menus/deleteItem/")}}' + id;
            {% endif %}

            }
        }

        var editItem = function (id) {
            var url = '{{ url("admin/menus/editItem")}}';
            var data = {
                'id':id,
                'menu_id': {{ menu.getId() }}
                {% if parent is defined %}, 'parent_id': {{ parent.getId() }}
                {% endif %}
            };

            PE.modal.open(url, data);
        }

        var manageItem = function (id) {
            window.location.href = window.location.pathname + '?parent_id=' + id;
        }

    </script>

{% endblock %}

{% block content %}

    <div class="row-fluid">
        <div class="menu_manage_header">
            <h3><a href="{{ url("admin/menus")}}" class='btn'>{{ "<< Back" | trans }}</a>
                {% if parent is defined %}
                    |
                    {% for p in parents %}
                        <a href="{{ url("admin/menus/manage/")}}{{ menu.getId() }}{% if p.getParentId() is not null %}?parent_id={{ p.getParentId() }}{% endif %}"
                           class='btn'>{{ p.getTitle() }}</a>
                        |
                    {% endfor %}
                {% endif %}
                {{ "Manage menu" | trans }}
                "{{ menu.getName() }}"</h3>
            <button class="btn btn-primary" onclick='requestAddItem();'>{{ 'Add new item'|trans }}</button>
            <div id="label-saved" class="label label-success">{{ 'Saved...'|trans }}</div>
        </div>
        <div class="menu_manage_body">
            <ul id="items">
                {% for item in items %}
                    <li element_id="{{ item.getId() }}">
                        <div class="item_title"><i class="icon-move"></i>{{ item.getTitle() }}
                            | {{ 'Items: '|trans }}{{ item.getMenuItem().count() }}</div>
                        <div class="item_options">
                            <a class="btn btn-success" href="javascript:;"
                               onclick="manageItem({{ item.getId() }});">{{ 'Manage'|trans }}</a>
                            <a class="btn btn-success" href="javascript:;"
                               onclick="editItem({{ item.getId() }});">{{ 'Edit'|trans }}</a>
                            <a class="btn btn-success" href="javascript:;"
                               onclick="deleteItem({{ item.getId() }});">{{ 'Remove'|trans }}</a>
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
