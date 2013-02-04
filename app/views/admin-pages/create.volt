{% extends "layouts/admin.volt" %}

{% block title %}{{ "Page Creation"|trans }}{% endblock %}
{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            {{ form.render() }}
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
