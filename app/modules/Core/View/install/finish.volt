{#
  PhalconEye

  LICENSE

  This source file is subject to the new BSD license that is bundled
  with this package in the file LICENSE.txt.

  If you did not receive a copy of the license and are unable to
  obtain it through the world-wide-web, please send an email
  to lantian.ivan@gmail.com so we can send you a copy immediately.
#}
{% extends "Install/layout.volt" %}

{% block title %}
    {{ 'Installation'|trans }}
{% endblock %}

{% block header %}
    {{ partial('/Install/header') }}
{% endblock %}

{% block content %}
    {% set action = 'finish' %}
    {{ partial('/Install/steps') }}

    <div class="thanks_message">
        {{ 'Specify admin account'|trans }}
    </div>
    <div>
        {{ form.render() }}
    </div>
{% endblock %}