{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.

   Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
#}

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

        {{ form.render('partials/form/default') }}

    {% endif %}
{% endblock %}