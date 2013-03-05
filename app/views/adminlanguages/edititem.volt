{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

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