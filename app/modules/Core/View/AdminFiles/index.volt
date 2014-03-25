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

{% block title %}{{ "Files management"|i18n }}{% endblock %}

{% block content %}
    <style type="text/css">
        iframe{
            min-width: 800px;
            min-height: 500px;
            width: 90%;
            height: 90%;
            border: 1px solid #cccccc;
            width: 90%;
            height: 90%;
        }

        .row-fluid{
            height: 100%;
        }
    </style>

    <div class="span12">
        <div class="row-fluid">
            <iframe style="" src="{{ url('external/pydio/index_shared.php')}}"></iframe>
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
