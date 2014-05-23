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

{% block title %}{{ page.title |i18n }}{% endblock %}

{% block head %}
    {% if page.keywords %}
        <meta name="keywords" content="{{ page.keywords |i18n }}" />
    {% endif %}
    {% if page.description %}
        <meta name="description" content="{{ page.description |i18n }}" />
    {% endif %}
{% endblock %}

{% block content %}

    {{ partial("partials/layout", ['page': page, 'content': content]) }}


    {#{% if helper('acl').isAllowed('\Core\Model\Page', 'show_views') %}#}
        {#<div class="page_views">{{ 'View count:'|i18n }}{{ page.view_count }}</div>#}
    {#{% endif %}#}

    {#{{ helper('acl').getAllowed('\Core\Model\Page', 'page_footer') }}#}


    <div class="clear"></div>
{% endblock %}


