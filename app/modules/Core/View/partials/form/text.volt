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

{{ form.openTag() }}
<div>
    <div class="form_header">
        <h3>{{ form.getTitle() }}</h3>

        <p>{{ form.getDescription() }}</p>
    </div>

    {{ partial(form.getErrorsView(), ['form': form]) }}
    {{ partial(form.getNoticesView(), ['form': form]) }}

    <div class="form_elements">
        {% for element in form.getAll() %}
            {{ partial(form.getElementView(), ['element': element]) }}
        {% endfor %}
    </div>
    <div class="clear"></div>

    {% if form.useToken() %}
        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}">
    {% endif %}
</div>
{{ form.closeTag() }}