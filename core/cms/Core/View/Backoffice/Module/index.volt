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

{% extends "Core/View/layouts/admin.volt" %}

{% block title %}{{ "Module settings"|i18n }}{% endblock %}

{% block content %}
    <div class="span12">
        <div class="row-fluid">
            {% if form is defined %}
                {{ form.render() }}
            {% else %}
                {{ 'This module has no settings...'|i18n }}
            {% endif %}
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
