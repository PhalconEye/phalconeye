{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
#}

{% extends "Core/View/layouts/modal.volt" %}

{% block title %}
    {{ 'Export translations'|i18n }}
{% endblock %}

{% block body %}
    {{ form.render() }}
{% endblock %}

{% block footer %}
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">{{ "Close"|i18n }}</button>
        <button class="btn btn-primary"
                onclick="$('#modal form')[0].submit();$('#modal').modal('hide');">{{ "Export"|i18n }}</button>
    </div>
{% endblock %}
