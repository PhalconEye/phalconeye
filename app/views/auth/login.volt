{% extends "layouts/main.volt" %}

{% block title %}{{ 'Login'|trans }}{% endblock %}
{% block content %}
    {{ form.render() }}
{% endblock %}
