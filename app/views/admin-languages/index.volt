{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Languages'|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this language?" | trans}}')) {
                window.location.href = '{{ url("admin/languages/delete/")}}' + id;
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
            <div class="languages_header">
                <h1>{{ 'Languages' | trans }}</h1>
                <button onclick="window.location.href='{{ url("admin/languages/compile") }}'; return false;" class="btn btn-primary button-loading" data-loading-text="{{ "Compiling..." | trans }}">{{ "Compile languages" | trans }}</button>
                <div class="clear"></div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>{{ 'Id' | trans }}</th>
                    <th>{{ 'Name' | trans }}</th>
                    <th>{{ 'Locale' | trans }}</th>
                    <th>{{ 'Icon' | trans }}</th>
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
                            {{ item.getLocale() }}
                        </td>
                        <td>
                            <img alt='' src='{{ item.getIcon() }}'/>
                        </td>
                        <td>
                            {{ link_to("admin/languages/edit/" ~ item.getId(), 'Edit' | trans) }}
                            {{ link_to("admin/languages/manage/" ~ item.getId(), 'Manage' | trans) }}
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
