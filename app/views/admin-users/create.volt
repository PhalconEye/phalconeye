{% extends "layouts/admin.volt" %}

{% block title %}{{ "User Creation"|trans }}{% endblock %}
{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            <h1>{{ 'User Creation' | trans }}</h1>
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
