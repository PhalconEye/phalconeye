{% extends "layouts/admin.volt" %}

{% block title %}{{ "Performance settings"|trans }}{% endblock %}

{% block content %}
    <div class="span12">
        <div class="row-fluid">
            {{ form.render() }}
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
