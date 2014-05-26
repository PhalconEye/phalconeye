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

{%- extends "../../Core/View/layouts/widget.volt" -%}

{%- block content -%}
    <div class="header_widget">
    {% if show_title is 1 or not(logo is empty)%}
        <div class="header_logo">
            <a href="{{ url() }}">
                {% if not(logo is empty) %}
                <img alt='{{ site_title }}' src="{{ url(logo) }}"/>
                {% endif %}
                {% if show_title is 1 %}{{ site_title }}{% endif %}
            </a>
        </div>
    {% endif %}

    {% if show_auth is 1 %}
        <div class="header_auth">
        {% if not helper('user', 'user').isUser() %}
            <a href="{{ url('login') }}">{{ 'Login' |i18n }}</a>&nbsp;
            |
            <a href="{{ url('register') }}">{{ 'Register' |i18n }}</a>
        {% else %}
            {{ 'Welcome, ' |i18n }}{{ helper('user', 'user').current().username }}&nbsp;
            |
            {% if helper('acl', 'core').isAllowed('AdminArea', 'access') %}
                <a href="{{ url('admin') }}">{{ 'Admin panel' |i18n }}</a>
            {% endif %}
            <a href="{{ url('logout') }}">{{ 'Logout' |i18n }}</a>
        {% endif %}
        </div>
    {% endif %}
    <div class="clear"></div>
    </div>
{%- endblock -%}
