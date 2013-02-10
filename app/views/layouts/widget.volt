{# widget.volt #}

<div class="widget_wrapper">

    {% if title is defined %}
    <div class="widget_header">
        {{ title }}
    </div>
    {% endif %}

    <div class="widget_container">
        {% block content %}
        {% endblock %}
    </div>
</div>
