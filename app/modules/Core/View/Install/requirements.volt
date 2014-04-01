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

{% extends "Install/layout.volt" %}

{% block title %}
    {{ 'Installation | Database'|i18n }}
{% endblock %}

{% block content %}
    {% set action = 'requirements' %}
    {{ partial('/Install/navigation') }}

    <div>
        <table>
            <thead>
            <tr>
                <th class="table-column-left">
                    {{ 'Requirement'|i18n }}
                </th>
                <th class="requirement_passed">
                    {{ 'Passed'|i18n }}
                </th>
            </tr>
            </thead>
            <tbody>
            {% for req in reqs %}
                <tr>
                    <td class="table-column-name table-column-left">
                        {{ req['name'] }}
                    </td>
                    <td class="requirement_passed">
                        {% if req['passed'] %}
                            <span class="glyphicon glyphicon-ok"></span>
                        {% else %}
                            <span class="glyphicon glyphicon-remove"></span>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            {% for path in pathInfo %}
                <tr>
                    <td class="table-column-name table-column-left">
                        {{ 'Writable'|i18n }} "{{ path['name'] }}"
                    </td>
                    <td class="requirement_passed">
                        {% if path['is_writable'] %}
                            <span class="glyphicon glyphicon-ok"></span>
                        {% else %}
                            <span class="glyphicon glyphicon-remove"></span>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>

        {% if passed %}
            <a href="{{ url('install/database') }} " class="proceed btn-primary">{{ 'Next'|i18n }}</a>
        {% else %}
            <div class="error">{{ 'Please, check all requirements and check pathes.'|i18n }}</div>
        {% endif %}
    </div>
{% endblock %}