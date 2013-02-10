{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Pages' | trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this page?" | trans }}')) {
                window.location.href = '/admin/pages/delete/' + id;
            }
        }
    </script>
{% endblock %}

{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            <h1>{{ 'Pages' | trans }}</h1>
            <table class="admin_table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Title' | trans }}</th>
                    <th>{{ 'Url' | trans }}</th>
                    <th>{{ 'Layout' | trans }}</th>
                    <th>{{ 'Controller' | trans }}</th>
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
                            {{ item.getTitle() }}
                        </td>
                        <td>
                            {{ item.getUrl() }}
                        </td>
                        <td>
                            {{ item.getLayout() }}
                        </td>
                        <td>
                            {{ item.getController() }}
                        </td>
                        <td>
                            {{ link_to("admin/pages/manage/" ~ item.getId(), 'Manage' | trans) }}
                            {% if item.getType() is null %}
                                {{ link_to("admin/pages/edit/" ~ item.getId(), 'Edit' | trans) }}
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
