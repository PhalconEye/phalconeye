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
{% if hideFooter is not defined %}
    <div class="modal-footer">
        {% if hideClose is not defined %}
            <button class="btn" data-dismiss="modal" aria-hidden="true">{{ "Close"|trans }}</button>
        {% endif %}

        {% if hideSave is not defined %}
            <button class="btn btn-primary btn-save">{{ "Save changes"|trans }}</button>
        {% endif %}
    </div>
{% endif %}

