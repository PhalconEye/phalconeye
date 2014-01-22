{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.

   Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
#}

{% extends "../../Core/View/layouts/admin.volt" %}


{% block title %}{{ 'Users'|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this user?" | trans}}')) {
                window.location.href = '{{ url(['for':'admin-users-delete'])}}' + id;
            }
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
        <div class="row-fluid">
            <h2>{{ 'Users' | trans }} ({{ paginator.items | length }})</h2>
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
                            {{ link_to(['for':'admin-users-view', 'id':item.id], item.id) }}
                        </td>
                        <td>
                            {{ item.username }}
                        </td>
                        <td>
                            {{ item.email }}
                        </td>
                        <td>
                            {{ item.getRole().name }}
                        </td>
                        <td>
                            {{ item.creation_date }}
                        </td>
                        <td>
                            {{ link_to(['for':'admin-users-edit', 'id':item.id], 'Edit' | trans) }}
                            {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.id ~');return false;') }}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
            {{ partial("paginator") }}
        </div>
        <!--/ row -->
    </div><!--/span-->

{% endblock %}
