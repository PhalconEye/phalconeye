{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Edit translation'|trans }}
{% endblock %}

{% block body %}


    {% if edited is defined %}
        <script type="text/javascript">

            $('#modal').modal('hide');
            window.location.reload();

        </script>
    {% else %}

        {{ form.render() }}

    {% endif %}
{% endblock %}