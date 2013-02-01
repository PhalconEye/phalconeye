<div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
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
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">{{ "Close"|trans }}</button>
        <button class="btn btn-primary">{{ "Save changes"|trans }}</button>
    </div>
</div>
