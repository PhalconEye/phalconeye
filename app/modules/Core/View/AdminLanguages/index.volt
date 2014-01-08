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

{% block title %}{{ 'Languages'|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this language?" | trans}}')) {
                window.location.href = '{{ url(['for':'admin-languages-delete'])}}' + id;
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
            <div class="languages_header">
                <h2>{{ 'Languages' | trans }} ({{ paginator.items | length }})</h2>
                <button onclick="window.location.href='{{ url(['for':'admin-languages-compile'])}}'; return false;" class="btn btn-primary button-loading" data-loading-text="{{ "Compiling..." | trans }}">{{ "Compile languages" | trans }}</button>
                <div class="clear"></div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Name' | trans }}</th>
                    <th>{{ 'Language' | trans }}</th>
                    <th>{{ 'Locale' | trans }}</th>
                    <th>{{ 'Icon' | trans }}</th>
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
                            {{ item.name }}
                        </td>
                        <td>
                            {{ item.language }}
                        </td>
                        <td>
                            {{ item.locale }}
                        </td>
                        <td>
                            {% if item.icon is empty %}
                                No flag image.
                            {% else %}
                            <img alt='' src='{{ url(item.icon) }}'/>
                            {% endif %}
                        </td>
                        <td>
                            {{ link_to(['for':'admin-languages-manage', 'id':item.id], 'Manage' | trans) }}
                            {{ link_to(['for':'admin-languages-edit', 'id':item.id], 'Edit' | trans) }}
                            {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.id ~');return false;') }}
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
