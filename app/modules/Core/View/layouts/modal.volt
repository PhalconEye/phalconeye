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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="modal_label">
        {% block title %}
        {% endblock %}
    </h3>
</div>
<div class="modal-body">
    {% block body %}
    {% endblock %}
</div>
{% block footer %}
{% endblock %}
{% if disableFooter is not defined %}
    <div class="modal-footer">
        {% if hideClose is not defined %}
            <button class="btn" data-dismiss="modal" aria-hidden="true">{{ "Close"|i18n }}</button>
        {% endif %}

        {% if hideSave is not defined %}
            <button class="btn btn-primary btn-save">{{ "Save"|i18n }}</button>
        {% endif %}
    </div>
{% endif %}

