{% extends "../../../views/layouts/widget.volt" %}

{% block content %}
    <div class="header_widget">
        <div class="header_logo">
            <a href="/">
                <img alt='{{ site_title }}' src="{{ logo }}"/>
                {% if show_title is 1 %}{{ site_title }}{% endif %}
            </a>
        </div>

        {% if show_auth is 1 %}
            <div class="header_auth">
                {% if viewer().getId() is 0 %}
                <a href="/login">{{ 'Login' | trans }}</a>&nbsp;|
                <a href="/register">{{ 'Register' | trans }}</a> </span>
                {% else %}
                    {{ 'Welcome, ' |trans }}{{ viewer().getUserName() }}&nbsp;|
                    <a href="/logout">{{ 'Logout' | trans }}</a> </span>
                {% endif %}
            </div>
        {% endif %}
        <div class="clear"></div>
    </div>
{% endblock %}
