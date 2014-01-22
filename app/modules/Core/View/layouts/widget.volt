{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.

   Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
#}

{# widget.volt #}

<div class="widget_wrapper">

    {% if title is defined and title is not null %}
    <div class="widget_header">
        <h3>{{ title|trans }}</h3>
    </div>
    {% endif %}

    <div class="widget_container">
        {% block content %}
        {% endblock %}
    </div>
</div>
