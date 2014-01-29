{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
#}

{% extends "layouts/modal.volt" %}

{% block title %}
    {{ name|trans }}
{% endblock %}

{% block body %}

    {% if widget_index is defined %}
        <script type="text/javascript">

            setEditedWidgetIndex({{widget_index}});
            $('#modal').modal('hide');

        </script>
    {% else %}

    {{ form.render('partials/form/default') }}

    {% endif %}
{% endblock %}