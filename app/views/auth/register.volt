{% extends "layouts/main.volt" %}

{% block title %}{{ 'Register you account!'|trans }}{% endblock %}
{% block content %}
    {{ form.render() }}
{% endblock %}
