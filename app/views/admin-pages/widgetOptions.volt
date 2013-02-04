{% extends "layouts/modal.volt" %}

{% block title %}
    {{ name|trans }}
{% endblock %}

{% block body %}

    {% if params is defined %}
        <script type="text/javascript">

            setEditedWidgetParams('{{params}}');
            $('#modal').modal('hide');

        </script>
    {% else %}

    {{ form.render() }}

    {% endif %}
{% endblock %}