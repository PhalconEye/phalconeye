{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Export Package'|trans }}
{% endblock %}

{% block body %}
    {{ form.renderForm() }}

{% endblock %}

{% block footer %}
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">{{ "Close"|trans }}</button>
        <button class="btn btn-primary" onclick="$('#modal form')[0].submit();$('#modal').modal('hide');">{{ "Export"|trans }}</button>
    </div>
{% endblock %}
