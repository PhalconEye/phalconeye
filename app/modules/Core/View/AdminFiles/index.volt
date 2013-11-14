{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Files management"|trans }}{% endblock %}

{% block content %}
    <style type="text/css">
        iframe{
            min-width: 800px;
            min-height: 500px;
            width: 90%;
            height: 90%;
            border: 1px solid #3f3f3f;
            width: 90%;
            height: 90%;
            border-radius: 5px;

        }

        .row-fluid{
            height: 100%;
        }
    </style>

    <div class="span12">
        <div class="row-fluid">
            <iframe style="" src="{{ url('external/ajaxplorer')}}"></iframe>
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
