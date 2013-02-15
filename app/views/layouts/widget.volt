{# widget.volt #}

<div class="widget_wrapper">

    {% if title is defined and title is not null %}
    <div class="widget_header">
        <span class="icon-th-large"></span>{{ title|trans }}
    </div>
    {% endif %}

    <div class="widget_container">
        {% block content %}
        {% endblock %}
    </div>
</div>
