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

{% extends "layouts/main.volt" %}

{% block title %}{{ page.title |trans }}{% endblock %}

{% block head %}
    <meta name="keywords" content="{{ page.keywords |trans }}" />
    <meta name="description" content="{{ page.description |trans }}" />
{% endblock %}

{% block content %}

    {# TOP #}
    {% if "top" in (content|keys) %}
        <div id="general-content-full-top">
            {% for widget in content["top"] %}
                {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# LEFT #}
    {% if "left" in (content|keys) %}
        <div id="general-content-left">
            {% for widget in content["left"] %}
                {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# RIGHT #}
    {% if "right" in (content|keys) %}
        <div id="general-content-right">
            {% for widget in content["right"] %}
                {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}

    {# MIDDLE #}
    {% if "middle" in (content|keys) %}

        {# LEFT MIDDLE RIGHT #}
        {% if ("right" in (content|keys)) and ("left" in (content|keys)) %}
            <div id="general-content">
                {% for widget in content["middle"] %}
                    {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}
        {# MIDDLE RIGHT #}
        {% if ("right" in (content|keys)) and ("left" not in (content|keys)) %}
            <div id="general-content-column-left">
                {% for widget in content["middle"] %}
                    {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}
        {# LEFT MIDDLE#}
        {% if ("left" in (content|keys)) and ("right" not in (content|keys)) %}
            <div id="general-content-column-right">
                {% for widget in content["middle"] %}
                    {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
                {% endfor %}
            </div>

            {# FULL MIDDLE#}
        {% endif %}

        {% if ("right" not in (content|keys)) and ("left" not in (content|keys)) %}
            <div id="general-content-full">
                {% for widget in content["middle"] %}
                    {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
                {% endfor %}
            </div>
        {% endif %}

    {% endif %}

    {# BOTTOM #}
    {% if "bottom" in (content|keys) %}
        <div id="general-content-full-bottom">
            {% for widget in content["bottom"] %}
                {{ helper('renderer', 'core').renderWidget(widget.widget_id, widget.getParams()) }}
            {% endfor %}
        </div>
    {% endif %}


    {#{% if helper('security').isAllowed('\Core\Model\Page', 'show_views') %}#}
        {#<div class="page_views">{{ 'View count:'|trans }}{{ page.view_count }}</div>#}
    {#{% endif %}#}

    {#{{ helper('security').getAllowed('\Core\Model\Page', 'page_footer') }}#}


    <div class="clear"></div>
{% endblock %}


