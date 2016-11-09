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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
#}

{%- extends "Core/View/layouts/widget.volt" -%}

{%- block content -%}
    <div class="header_widget">
    {% if show_title is 1 or not(logo is empty)%}
        <div class="header_logo">
            <a href="{{ url() }}">
                {% if not(logo is empty) %}
                <img alt='{{ site_title }}' src="{{ url(logo) }}"/>
                {% endif %}
                {% if show_title is 1 %}
                <h1>{{ site_title }}</h1>
                {% endif %}
            </a>
        </div>
    {% endif %}

    {% if show_auth is 1 %}
        <div class="header_auth">
        {% if not helper('User', 'User').isUser() %}
            <a href="{{ url(['for': 'login']) }}">{{ 'Login' |i18n }}</a>
            <a href="{{ url(['for': 'register']) }}">{{ 'Register' |i18n }}</a>
        {% else %}
            <span>{{ 'Welcome, ' |i18n }}{{ helper('User', 'User').current().username }}</span>
            {% if helper('Acl', 'Core').isAllowed('BackofficeArea', 'access') %}
                <a href="{{ url(['for': 'backoffice-home']) }}">{{ 'Admin panel' |i18n }}</a>
            {% endif %}
            <a href="{{ url(['for': 'logout']) }}">{{ 'Logout' |i18n }}</a>
        {% endif %}
        </div>
    {% endif %}
    </div>
{%- endblock -%}
