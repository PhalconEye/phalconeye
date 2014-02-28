{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Manage language"|i18n }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var requestAddItem = function () {
            var url = '{{ url(['for':'admin-languages-create-item'])}}';
            var data = {
                'language_id': {{ lang.id }}
            };

            PhalconEye.widget.modal.open(url, data);
        };

        var editItem = function (id) {
            var url = '{{ url(['for':'admin-languages-edit-item'])}}' + id;
            var data = {
                'id': id,
                'language_id': {{ lang.id }}
            };

            PhalconEye.widget.modal.open(url, data);
        };

        var showUntranslated = function (element) {
            var flag = 1;

            if (PhalconEye.widget.grid.getParam('untranslated')) {
                flag = 0;
                element.removeClass('btn-success');
            }
            else {
                element.addClass('btn-success');
            }

            PhalconEye.widget.grid.setParam('untranslated', flag);
            PhalconEye.widget.grid.load();
        };
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
        <div class="language_manage_header">
            <h3>
                <a href="{{ url(['for': 'admin-languages']) }}">{{ "Languages" |i18n }}</a> > {{ lang.name }}
            </h3>

            <button class="btn btn-primary" onclick='requestAddItem();'>{{ 'Add new item'|i18n }}</button>
            {% if (lang.language != constant('\Engine\Config::CONFIG_DEFAULT_LANGUAGE')) %}
                <button class="btn btn-primary" onclick='showUntranslated($(this));'>{{ 'Show untranslated'|i18n }}</button>
                <a class="btn btn-info"
                   href='{{ url(['for': 'admin-languages-synchronize', 'id': lang.id]) }}'>{{ 'Synchronize'|i18n }}</a>
            {% endif %}
            <form class="navbar-search pull-right" method="GET"
                  action="{{ url(['for': 'admin-languages-manage'])~lang.id }}">
                {% if search is defined %}
                    <div class="glyphicon glyphicon-remove"
                         onclick="window.location.href='{{ url(['for': 'admin-languages-manage'])~lang.id }}'"></div>
                {% endif %}
                <input name="search" type="text" class="search-query form-control" placeholder="{{ 'Search' |i18n }}"
                       value="{{ search }}"/>

                <div class="glyphicon glyphicon-search" onclick="$(this).parent().submit();"></div>
            </form>
        </div>
        <div class="language_manage_body">
            {{ grid.render() }}
        </div>
    </div>
{% endblock %}