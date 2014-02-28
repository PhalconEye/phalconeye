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
    {{ 'Create new menu item'|i18n }}
{% endblock %}

{% block body %}

    <script type="text/javascript">
        var checkUrlType = function () {
            var value = $('input[name="url_type"]:checked').val();
            if (value == undefined || value == 0) {
                $('#url').parent().parent().show();
                $('#page').parent().parent().hide();
            }
            else {
                $('#url').parent().parent().hide();
                $('#page').parent().parent().show();
            }
        };

        $(document).ready(function () {
            $('input[name="url_type"]').click(function () {
                checkUrlType();
            });
            checkUrlType();
        });
    </script>

    {{ form.render() }}

{% endblock %}