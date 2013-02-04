{% extends "layouts/admin.volt" %}

{% block title %}Users{% endblock %}
{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            <h1>{{ 'Users' | trans }}</h1>
            <table class="admin_table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Username' | trans }}</th>
                    <th>{{ 'Email' | trans }}</th>
                    <th>{{ 'Creation Date' | trans }}</th>
                    <th>{{ 'Options' | trans }}</th>
                </tr>
                </thead>
                <tbody>
                {% for item in page.items %}
                    <tr>
                        <td>
                            {{ item.getId() }}
                        </td>
                        <td>
                            {{ item.getUsername() }}
                        </td>
                        <td>
                            {{ item.getEmail() }}
                        </td>
                        <td>
                            {{ item.getCreationDate() }}
                        </td>
                        <td>
                            {{ link_to("admin/users/edit/" ~ item.getId(), 'Edit' | trans) }}
                            {{ link_to("admin/users/delete/" ~ item.getId(), 'Delete' | trans) }}
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
