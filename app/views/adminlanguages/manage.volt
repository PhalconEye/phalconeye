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

{% block title %}{{ "Manage language"|trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var deleteItem = function (id) {
            if (confirm('{{ "Are you really want to delete this translation?" | trans}}')) {
                window.location.href = '{{ url(['for':'admin-languages-delete-item'])}}' + id + '?lang={{ lang.getId() }}';
            }
        }

        var requestAddItem = function () {
            var url = '{{ url(['for':'admin-languages-create-item'])}}';
            var data = {
                'language_id': {{ lang.getId() }}
            };

            PE.modal.open(url, data);
        }

        var editItem = function (id) {
            var url = '{{ url(['for':'admin-languages-edit-item'])}}' + id;
            var data = {
                'id':id,
                'language_id': {{ lang.getId() }}
            };

            PE.modal.open(url, data);
        }
    </script>
{% endblock %}

{% block content %}

    <div class="row-fluid">
        <div class="language_manage_header">
            <h3><a href="{{ url("admin/languages") }}" class='btn'>{{ "<< Back" | trans }}</a>
                | {{ "Manage language" | trans }}
                "{{ lang.getName() }}"</h3>
            <button class="btn btn-primary" onclick='requestAddItem();'>{{ 'Add new item'|trans }}</button>
        </div>
        <div class="language_manage_body">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ 'Original' | trans }}</th>
                    <th>{{ 'Translated' | trans }}</th>
                    <th>{{ 'Options' | trans }}</th>
                </tr>
                </thead>
                <tbody>
                {% for item in paginator.items %}
                    <tr>
                        <td>
                            {{ item.getOriginal() }}
                        </td>
                        <td>
                            {{ item.getTranslated() }}
                        </td>
                        <td>
                            {{ link_to(null, 'Edit' | trans, "onclick" : 'editItem(' ~ item.getId() ~ ');return false;') }}
                            {{ link_to(null, 'Delete' | trans, "onclick": 'deleteItem('~ item.getId() ~');return false;') }}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
            {{ partial("partials/paginator") }}
        </div>
    </div>



{% endblock %}
