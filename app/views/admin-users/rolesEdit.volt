{% extends "layouts/admin.volt" %}

{% block title %}{{ "Role Editing"|trans }}{% endblock %}
{% block content %}
    <div class="span3 admin-sidebar">
        {{ navigationMain.render() }}
        <br/>
        {{ navigationCreation.render() }}
    </div>

    <div class="span9">
        <div class="row-fluid">
            {{ form.render() }}
        </div>
        <!--/row-->
    </div><!--/span-->

{% endblock %}
