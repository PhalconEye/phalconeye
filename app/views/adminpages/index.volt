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

{% block title %}{{ 'Pages' | trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this page?" | trans }}')) {
                window.location.href = '{{ url(['for':'admin-pages-delete'])}}' + id;
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
            <h2>{{ 'Pages' | trans }} ({{ paginator.items | length }})</h2>
            <table class="table">
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
                            {{ link_to(['for':'admin-pages-manage', 'id':item.getId()], 'Manage' | trans) }}
                            {% if item.getType() is null %}
                                {{ link_to(['for':'admin-pages-edit', 'id':item.getId()], 'Edit' | trans) }}
                                {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.getId() ~');return false;') }}
                            {% elseif item.getType() is 'home' %}
                                {{ link_to(['for':'admin-pages-edit', 'id':item.getId()], 'Edit' | trans) }}
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
