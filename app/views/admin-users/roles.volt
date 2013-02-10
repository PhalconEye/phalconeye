{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Roles'|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this role?" | trans}}')) {
                window.location.href = '/admin/users/roles-delete/' + id;
            }
        }
    </script>
{% endblock %}

{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigationMain.render() }}
        <br/>
        {{ navigationCreation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            <h1>{{ 'Roles' | trans }}</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Name' | trans }}</th>
                    <th>{{ 'Description' | trans }}</th>
                    <th>{{ 'Is default?' | trans }}</th>
                    <th>{{ 'Options' | trans }}</th>
                </tr>
                </thead>
                <tbody>
                {% for item in paginator.items %}
                    <tr>
                        <td>
                            {{ item.getId() }}
                        </td>
                        <td>
                            {{ item.getName() }}
                        </td>
                        <td>
                            {{ item.getDescription() }}
                        </td>
                        <td>
                            {% if item.getIsDefault() %}
                            {{ 'Yes' |trans }}
                            {% else %}
                            {{ 'No' |trans }}
                            {% endif %}
                        </td>
                        <td>
                            {{ link_to("admin/users/roles-edit/" ~ item.getId(), 'Edit' | trans) }}
                            {% if not item.getUndeletable() %}
                            {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.getId() ~');return false;') }}
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
            {{ partial("partials/paginator") }}
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
