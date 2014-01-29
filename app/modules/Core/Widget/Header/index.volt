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

{%- extends "../../View/layouts/widget.volt" -%}

{%- block content -%}
    <div class="header_widget">
        <div class="header_logo">
        <a href="/">
            <img alt='{{ site_title }}' src="{{ logo }}"/>
            {% if show_title is 1 %}{{ site_title }}{% endif %}
        </a>
    </div>

    {% if show_auth is 1 %}
        <div class="header_auth">
        {% if not helper('user', 'user').isUser() %}
            <a href="{{ url('login') }}">{{ 'Login' | trans }}</a>&nbsp;
            |
            <a href="{{ url('register') }}">{{ 'Register' | trans }}</a>
        {% else %}
            {{ 'Welcome, ' |trans }}{{ helper('user', 'user').current().username }}&nbsp;
            |
            {% if helper('security', 'core').isAllowed('AdminArea', 'access') %}
                <a href="{{ url('admin') }}">{{ 'Admin panel' | trans }}</a>
            {% endif %}
            <a href="{{ url('logout') }}">{{ 'Logout' | trans }}</a>
        {% endif %}
        </div>
    {% endif %}
    <div class="clear"></div>
    </div>
{%- endblock -%}
