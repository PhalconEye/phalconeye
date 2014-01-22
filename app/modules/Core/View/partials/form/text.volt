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

{{ form.openTag() }}
<div>
    <div class="form_header">
        <h3>{{ form.getTitle() }}</h3>

        <p>{{ form.getDescription() }}</p>
    </div>

    {{ partial(resolveView("partials/form/text/errors", 'core'), ['form': form]) }}
    {{ partial(resolveView("partials/form/text/notices", 'core'), ['form': form]) }}

    <div class="form_elements">
        {% for element in form.getAll() %}
            {{ partial(resolveView("partials/form/text/element", 'core'), ['element': element]) }}
        {% endfor %}
    </div>
    <div class="clear"></div>

    {% if form.useToken() %}
        <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getToken() }}">
    {% endif %}
</div>
{{ form.closeTag() }}