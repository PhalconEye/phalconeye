{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{# main.volt #}
<!DOCTYPE html>
<html>
<head>
    <title><?php echo \Core\Model\Settings::getSetting('system_title', '') ?> | {% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="PhalconEye - Open Source Content Management System" />
    <link rel="stylesheet" href="{{ url('css/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ url('themes/' ~ helper('core').setting('system_theme', 'default') ~ '/theme.css') }}"/>
    {{ javascript_include("js/jquery/jquery-1.8.3.min.js") }}
    {{ javascript_include("js/bootstrap/bootstrap.min.js") }}


    {%- block head -%}

    {%- endblock -%}

</head>
<body>
<div id="wrapper" class="container">
    <div id="header" class="masthead">
        {{ helper('core').renderContent('header') }}
        {% block header %}
        {% endblock %}
    </div>
    <div class="container">
        <div class="row">
            {{ content() }}
        </div><!--/row-->

        <div class="row">
            {%- block content -%}
            {%- endblock -%}
        </div><!--/row-->
    </div>
    <div id="footer">
        {{ helper('core').renderContent('footer') }}
        {%- block footer -%}
        {%- endblock -%}
    </div>
</div>

</body>
</html>