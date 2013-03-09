{#
  PhalconEye

  LICENSE

  This source file is subject to the new BSD license that is bundled
  with this package in the file LICENSE.txt.

  If you did not receive a copy of the license and are unable to
  obtain it through the world-wide-web, please send an email
  to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Edit Access"|trans }}{% endblock %}
{% block content %}

    <div class="row-fluid">
        <div class="access_edit_header">
            <h2><a href="{{ url(['for': 'admin-access']) }}" class='btn'>{{ "<< Back" | trans }}</a>
                | {{ 'Editing access rights of "%currentObject%", for:' | trans(['currentObject':currentObject]) }}</h2>
            <div class="current_role">

                <div class="btn-group">
                    <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        {{ currentRole.getName() }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        {% for role in roles %}
                            {% if currentRole.getId() == role.getId() %} {% continue %} {% endif %}
                            <li><a href="javascript:;" onclick="window.location.href += '?role={{ role.getId() }}';">{{ role.getName() }}</a></li>
                        {% endfor %}
                    </ul>
                </div>

            </div>
            <div class="clear"></div>
        </div>
        {{ form.render() }}
    </div>

{% endblock %}

