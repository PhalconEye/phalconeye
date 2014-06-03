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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
#}

{% extends "Core/View/layouts/widget.volt" %}

{% block content %}

<ul class="bxslider" data-slider-id="{{ slider_id }}">
{% for slide in slides %}
    {% if height > 0 %}
    <li><div style="min-height: {{ height }}px">{{ slide }}</div></li>
    {% else %}
    <li>{{ slide }}</li>
    {% endif %}
{% endfor %}
</ul>

<script type="application/javascript">
document.addEventListener('DOMContentLoaded', function() {
  $('.bxslider').filter('[data-slider-id="{{ slider_id }}"]').bxSlider({
    pause: {{ params['duration'] }},
    speed: {{ params['speed'] }},
    auto: ({{ params['auto'] }} == 1),
    autoHover: ({{ params['auto_hover'] }} == 1),
    controls: ({{ params['controls'] }} == 1),
    video: ({{ params['video'] }} == 1),
    pager: ({{ params['pager'] }} == 1),
    adaptiveHeight: {{ height > 0 ? 'false' : 'true' }},
    captions: true,
    nextText: "›",
    prevText : "‹"
  });
}, false);
</script>

{% endblock %}