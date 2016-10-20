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

{% extends "Cms/Install/View/layout.volt" %}

{% block title %}
    {{ 'Installation | Database'|i18n }}
{% endblock %}

{% block content %}
    {% set action = 'license' %}
    {{ partial('Cms/Install/View/navigation') }}

    <div class="content">
        <pre>
            {{- license -}}
        </pre>
    </div>
    <a href="{{ url('install/requirements') }} " class="proceed">{{ 'Accept'|i18n }}</a>
{% endblock %}