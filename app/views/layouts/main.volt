{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{# main.volt #}
<!DOCTYPE html>
<html>
<head>
    <title><?php echo Settings::getSetting('system_title', '') ?> | {% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="/public/themes/{{ helper('setting', 'system_theme', 'default') }}/theme.css"/>
    <meta name="generator" content="PhalconEye - Open Source Content Management System" />

    {% block head %}

    {% endblock %}

</head>
<body>
<div id="wrapper" class="container">
    <div id="header" class="masthead">
        {{ helper('renderContent', 'header') }}
        {% block header %}
        {% endblock %}
    </div>
    <div class="container">
        <div class="row">
            {{ content() }}
        </div><!--/row-->

        <div class="row">
            {% block content %}
            {% endblock %}
        </div><!--/row-->
    </div>
    <div id="footer">
        {{ helper('renderContent', 'footer') }}
        {% block footer %}
        {% endblock %}
    </div>
</div>

{{ javascript_include("js/jquery/jquery-1.8.3.min.js") }}
{{ javascript_include("js/bootstrap/bootstrap.min.js") }}


</body>
</html>