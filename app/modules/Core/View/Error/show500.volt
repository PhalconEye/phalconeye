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

{% extends "../../Core/View/layouts/main.volt" %}

{% block title %}
    {{ 'Internal Server Error'|i18n }}
{% endblock %}

{% block content %}
    <div class="error_container">
        <div class="error_block">
            <div class="error_page_title">
                500
            </div>
            <div class="error_page_description">
                Sorry, there is an error.
                {% if this.currentErrorCode is defined %}
                    Error code: <b>{{ this.currentErrorCode }}</b>.
                {% endif %}
            </div>
        </div>
    </div>
{% endblock %}

