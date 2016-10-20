{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
#}

{% extends "Core/View/layouts/admin.volt" %}

{% block title %}Admin panel{% endblock %}

{% block head %}
    {{ helper('assets').addJs('application/js/core/backoffice/dashboard.js') }}
{% endblock %}

{% block content %}
    <div class="dashboard">

        <main>
            <h1>{{ 'Dashboard' |i18n }}</h1>
            Some activity here... imagine it =)... coming soon (maybe in 0.5.0)...
        </main>

        <aside>
            <div>
                <h4>{{ 'Debug mode'|i18n }}</h4>
                <input name="debug" type="checkbox" data-href="{{ url(['for':'backoffice-mode'])}}" {% if debug %}checked{% endif %}>
            </div>
            <div>
                <h4>{{ 'Profiler'|i18n }}</h4>
                <input name="profiler" type="checkbox" data-href="{{ url(['for':'backoffice-profiler'])}}" {% if profiler %}checked{%
                endif %}>
            </div>
            <hr>
            <div>
                <a href="{{ url(['for':'backoffice-clear'])}}" class="btn btn-primary">{{ 'Clear cache'|i18n }}</a>
            </div>
        </aside>

    </div>
{% endblock %}
