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
    <title>{{ helper('core').Setting('system_title', '') }} | {% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="PhalconEye - Open Source Content Management System"/>

    {{ assets.outputCss() }}

    {%- block head -%}

    {%- endblock -%}

</head>
<body>
<div id="wrapper">
    <div id="header">
        {% if disableHeader is not defined %}
        {{ helper('core').renderContent('header') }}
        {% endif %}

        {%- block header -%}
        {%- endblock -%}
    </div>

    <div class="system-container">
        {{ content() }}
    </div>

    <div class="container">
        {%- block content -%}
        {%- endblock -%}
    </div>

    <div id="footer">
        {% if disableFooter is not defined %}
        {{ helper('core').renderContent('footer') }}
        {% endif %}
        {%- block footer -%}
        {%- endblock -%}
    </div>
</div>

{{ assets.outputJs() }}

</body>
</html>