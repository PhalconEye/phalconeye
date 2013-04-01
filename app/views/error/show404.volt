{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/main.volt" %}

{% block title %}
    {{ 'Not Found'|trans }}
{% endblock %}

{% block content %}
<div class="error_page_title">
    404
</div>
<div class="error_page_description">
    Page Not Found - The requested page could not be found
</div>
{% endblock %}

