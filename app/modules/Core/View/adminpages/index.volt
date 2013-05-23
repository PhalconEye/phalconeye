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
                            {{ item.id }}
                        </td>
                        <td>
                            {{ item.title }}
                        </td>
                        <td>
                            {{ item.url }}
                        </td>
                        <td>
                            {{ item.layout }}
                        </td>
                        <td>
                            {{ item.controller }}
                        </td>
                        <td>
                            {{ link_to(['for':'admin-pages-manage', 'id':item.id], 'Manage' | trans) }}
                            {% if item.type is null %}
                                {{ link_to(['for':'admin-pages-edit', 'id':item.id], 'Edit' | trans) }}
                                {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.id ~');return false;') }}
                            {% elseif item.type is 'home' %}
                                {{ link_to(['for':'admin-pages-edit', 'id':item.id], 'Edit' | trans) }}
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
            {{ partial("paginator") }}
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
