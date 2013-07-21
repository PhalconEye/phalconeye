{#
  PhalconEye

  LICENSE

  This source file is subject to the new BSD license that is bundled
  with this package in the file LICENSE.txt.

  If you did not receive a copy of the license and are unable to
  obtain it through the world-wide-web, please send an email
  to lantian.ivan@gmail.com so we can send you a copy immediately.
#}
{% extends "install/layout.volt" %}

{% block title %}
    {{ 'Installation | Database'|trans }}
{% endblock %}

{% block header %}
    {{ partial('/install/header') }}
{% endblock %}

{% block content %}
    {% set action = 'index' %}
    {{ partial('/install/steps') }}

    <div>
        <table>
            <thead>
            <tr>
                <th>
                    {{ 'Requirement'|trans }}
                </th>
                <th>
                    {{ 'Required Version / Installed Version'|trans }}
                </th>
                <th>
                    {{ 'Passed'|trans }}
                </th>
            </tr>
            </thead>
            <tbody>
            {% for req in reqs %}
                <tr>
                    <td class="table-column-name">
                        {{ req['name'] }}
                    </td>
                    <td>
                        {{ req['version'] }} / {{ req['installed_version'] }}
                    </td>
                    <td>
                        {% if req['passed'] %}
                            <img alt="Passed" src="/external/phalconeye/images/install/good.png"/>
                        {% else %}
                            <img alt="Not Passed" src="/external/phalconeye/images/install/bad.png"/>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>

        <table>
            <thead>
            <tr>
                <th class="table-column-left">
                    {{ 'Path'|trans }}
                </th>
                <th>
                    {{ 'Writable'|trans }}
                </th>
            </tr>
            </thead>
            <tbody>
            {% for path in pathInfo %}
                <tr>
                    <td class="table-column-name table-column-left">
                        {{ path['name'] }}
                    </td>
                    <td>
                        {% if path['is_writable'] %}
                            <img alt="Passed" src="/external/phalconeye/images/install/good.png"/>
                        {% else %}
                            <img alt="Not Passed" src="/external/phalconeye/images/install/bad.png"/>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>

        {% if passed %}
            <a href="/install/database" class="proceed">{{ 'Install'|trans }}</a>
        {% else %}
            <div class="error">{{ 'Please, install all requirements and check pathes.'|trans }}</div>
        {% endif %}
    </div>
{% endblock %}