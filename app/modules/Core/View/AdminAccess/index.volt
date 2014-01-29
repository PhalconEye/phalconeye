{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

{% block title %}{{ "Access Rights"|trans }}{% endblock %}
{% block content %}
<div class="span12">
    <div class="row-fluid">
        <div class="languages_header">
            <h2>{{ 'Available resources' | trans }}</h2>
            <div class="clear"></div>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>{{ 'Resource Name' | trans }}</th>
                <th>{{ 'Actions' | trans }}</th>
                <th>{{ 'Options' | trans }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {% for item in objects %}
                <tr>
                    <td>
                        {{ item.name }}
                    </td>
                    <td>
                        {{ item.actions }}
                    </td>
                    <td>
                        {{ item.options }}
                    </td>

                    <td>
                        {{ link_to('admin/access/edit?id='~item.name, 'Edit' | trans) }}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

