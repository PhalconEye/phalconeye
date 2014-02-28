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

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Edit Access"|i18n }}{% endblock %}
{% block content %}
<div class="span12">
    <div class="row-fluid">
        <div class="access_edit_header">
            <h2><a href="{{ url(['for': 'admin-access']) }}">{{ "Access Rights" |i18n }}</a>
                > {{ 'Editing access rights of "%currentObject%", for:' |i18n(['currentObject':currentObject]) }}</h2>
            <div class="current_role">

                <div class="btn-group">
                    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        {{ currentRole.name }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {% for role in roles %}
                            {% if currentRole.id == role.id %} {% continue %} {% endif %}
                            <li><a href="javascript:;" onclick="window.location.href += '&role={{ role.id }}';">{{ role.name }}</a></li>
                        {% endfor %}
                    </ul>
                </div>

            </div>
            <div class="clear"></div>
        </div>
        {{ form.render() }}
    </div>
</div>
{% endblock %}

