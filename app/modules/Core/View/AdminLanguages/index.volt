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

{% block title %}{{ 'Languages'|i18n }}{% endblock %}

{% block head %}
    {{ helper('assets').addJs('assets/js/core/admin/languages.js') }}
{% endblock %}

{% block header %}
    <div class="navbar navbar-header">
        {{ navigation.render() }}
    </div>
{% endblock %}

{% block content %}
    <div class="span12">
        <div class="row-fluid">
            <h2>{{ 'Languages' |i18n }} ({{ grid.getTotalCount() }})</h2>

            <div class="list-actions">
                <a href="{{ url(['for':'admin-languages-compile']) }}"
                   class="btn btn-primary">{{ "Compile languages" |i18n }}</a>
                <button class="btn btn-primary btn-import">{{ "Import..." |i18n }}</button>
                {{ form.render() }}
            </div>

            {{ grid.render() }}
        </div>
    </div>
{% endblock %}