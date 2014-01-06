{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
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
        {% if helper('user', 'user').isUser() %}
            <a href="{{ url('login') }}">{{ 'Login' | trans }}</a>&nbsp;
            |
            <a href="{{ url('register') }}">{{ 'Register' | trans }}</a>
        {% else %}
            {{ 'Welcome, ' |trans }}{{ helper('user', 'user').current().username }}&nbsp;
            |
            {% if helper('security').isAllowed('AdminArea', 'access') %}
                <a href="{{ url('admin') }}">{{ 'Admin panel' | trans }}</a>
            {% endif %}
            <a href="{{ url('logout') }}">{{ 'Logout' | trans }}</a>
        {% endif %}
        </div>
    {% endif %}
    <div class="clear"></div>
    </div>
{%- endblock -%}
