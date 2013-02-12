{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Add new translation'|trans }}
{% endblock %}

{% block body %}

    {% if created is defined %}
        <script type="text/javascript">
            window.location.reload();
        </script>
    {% else %}

        {{ form.render() }}

    {% endif %}
{% endblock %}