{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Create new menu item'|trans }}
{% endblock %}

{% block body %}

    <script type="text/javascript">
        var checkUrlType = function(){
            var value = $('input[name="url_type"]:checked').val();
            if (value == undefined || value == 0 ){
                $('#url').parent().parent().show();
                $('#page').parent().parent().hide();
            }
            else{
                $('#url').parent().parent().hide();
                $('#page').parent().parent().show();
            }
        }
        $(document).ready(function() {
            $('input[name="url_type"]').click(function(){
                checkUrlType();
            });
            checkUrlType();
        });
    </script>

    {% if created is defined %}
        <script type="text/javascript">

            addItem({{ created.getId() }}, '{{ created.getTitle() }}');
            $('#modal').modal('hide');

        </script>
    {% else %}

        {{ form.render() }}

    {% endif %}
{% endblock %}