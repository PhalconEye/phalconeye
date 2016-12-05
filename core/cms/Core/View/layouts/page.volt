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

{% extends "Core/View/layouts/main.volt" %}

{% block title %}{{ page.title |i18n }}{% endblock %}

{% block head %}
    {% if page.keywords %}
        <meta name="keywords" content="{{ page.keywords |i18n }}"/>
    {% endif %}
    {% if page.description %}
        <meta name="description" content="{{ page.description |i18n }}"/>
    {% endif %}
{% endblock %}

{% block content %}

    {% if page.use_dynamic_layout %}
        {% set layoutView = view.resolvePartial("layouts/page/dynamic" , 'Core') %}
    {% else %}
        {% set layoutView = view.resolvePartial("layouts/page/" ~ page.layout , 'Core') %}
    {% endif %}

    {% if page.cache_lifetime %}
        {% set lifetime = page.cache_lifetime %}
        {% cache ("page_" ~ page.id) lifetime %}
        {{ partial(layoutView, ['page': page, 'content': content]) }}
        {% endcache %}
    {% else %}
        {{ partial(layoutView, ['page': page, 'content': content]) }}
    {% endif %}


    {#ACL Examples#}
    {#{% if helper('Acl', 'Core').isAllowed('\Core\Model\Page', 'show_views') %}#}
    {#<div class="page_views">{{ 'View count:'|i18n }}{{ page.view_count }}</div>#}
    {#{% endif %}#}

    {#{{ helper('Acl', 'Core').getAllowed('\Core\Model\Page', 'page_footer') }}#}


    <div class="clear"></div>
{% endblock %}


