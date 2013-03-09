{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/main.volt" %}

{% block title %}{{ page.getTitle() |trans }}{% endblock %}

{% block head %}
    <meta name="keywords" content="{{ page.getKeywords() |trans }}" />
    <meta name="description" content="{{ page.getDescription() |trans }}" />
{% endblock %}

{% block content %}

    {# TOP #}
    {% if helper('contains', "top", content|keys) %}
        <div id="general-content-full-top">
            {% for widget in content["top"] %}
                {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# LEFT #}
    {% if helper('contains', "left", content|keys) %}
        <div id="general-content-left">
            {% for widget in content["left"] %}
                {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# RIGHT #}
    {% if helper('contains', "right", content|keys) %}
        <div id="general-content-right">
            {% for widget in content["right"] %}
                {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# MIDDLE #}
    {% if helper('contains', "middle", content|keys) %}

        {# LEFT MIDDLE RIGHT #}
        {% if helper('contains', "right", content|keys) and helper('contains', "left", content|keys) %}
            <div id="general-content">
                {% for widget in content["middle"] %}
                    {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}
        {# MIDDLE RIGHT #}
        {% if helper('contains', "right", content|keys) and not (helper('contains', "left", content|keys)) %}
            <div id="general-content-column-left">
                {% for widget in content["middle"] %}
                    {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}
        {# LEFT MIDDLE#}
        {% if helper('contains', "left", content|keys) and not (helper('contains', "right", content|keys)) %}
            <div id="general-content-column-right">
                {% for widget in content["middle"] %}
                    {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
                {% endfor %}
            </div>

            {# FULL MIDDLE#}
        {% endif %}

        {% if not (helper('contains', "right", content|keys)) and not (helper('contains', "left", content|keys)) %}
            <div id="general-content-full">
                {% for widget in content["middle"] %}
                    {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}

    {% endif %}

    {# BOTTOM #}
    {% if helper('contains', "bottom", content|keys) %}
        <div id="general-content-full-bottom">
            {% for widget in content["bottom"] %}
                {{ helper('renderWidget', widget.getWidgetId(), widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}


    {% if helper('Allowed', 'Page', 'show_views') %}
        <div class="page_views">{{ 'View count:'|trans }}{{ page.getViewCount() }}</div>
    {% endif %}

    {{ helper('GetAllowed', 'Page', 'page_footer') }}


    <div class="clear"></div>
{% endblock %}


