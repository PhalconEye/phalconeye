{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Edit menu item'|trans }}
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

    {% if edited is defined %}
        <script type="text/javascript">

            $('#modal').modal('hide');
            window.location.reload();

        </script>
    {% else %}

        {{ form.render() }}

    {% endif %}
{% endblock %}