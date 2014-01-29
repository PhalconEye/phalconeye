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

{% block title %}Index{% endblock %}

{% block head %}
    <script type="text/javascript">
        var setMode = function (debug) {
            $.ajax({
                type: "get",
                url: '{{ url(['for':'admin-mode'])}}',
                data: {
                    'debug': debug
                },
                dataType: 'json',
                success: function () {
                    window.location.reload();
                }
            });
        }
    </script>
{% endblock %}

{% block content %}

    <div class="dashboard">
        <div class="span6">
            <h1>{{ 'Dashboard' | trans }}</h1>
        </div>

        <div class="span2 dashboard-sidebar">
            <h4><span>{{ 'System mode'|trans }}</span></h4>

            <div class="btn-group" data-toggle="buttons-radio">
                <button onclick="setMode(0);" type="button" class="btn btn-primary{% if not debug %} active{% endif %}">
                    Production
                </button>
                <button onclick="setMode(1);" type="button" class="btn{% if debug %} active{% endif %}">Debug</button>
            </div>

        </div>
    </div>
{% endblock %}
