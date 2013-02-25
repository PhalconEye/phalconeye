{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Users'|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this user?" | trans}}')) {
                window.location.href = '{{ url("admin/users/delete/")}}' + id;
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
            <h1>{{ 'Users' | trans }}</h1>
            <table class="table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Username' | trans }}</th>
                    <th>{{ 'Email' | trans }}</th>
                    <th>{{ 'Role' | trans }}</th>
                    <th>{{ 'Creation Date' | trans }}</th>
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
                            {{ item.getUsername() }}
                        </td>
                        <td>
                            {{ item.getEmail() }}
                        </td>
                        <td>
                            {{ item.getRole().getName() }}
                        </td>
                        <td>
                            {{ item.getCreationDate() }}
                        </td>
                        <td>
                            {{ link_to("admin/users/edit/" ~ item.getId(), 'Edit' | trans) }}
                            {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.getId() ~');return false;') }}
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
