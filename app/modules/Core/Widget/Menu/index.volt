{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{% extends "../../View/layouts/widget.volt" %}

{% block content %}

    <script type="text/javascript">
        $(document).ready(function(){
            $(".navbar .navbar-inner a").tooltip({
                html:"true"
            });
        });
    </script>

    <div class="navbar">
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
    </div>
{% endblock %}
