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

<fieldset{{ fieldSet.renderAttributes() }}>
    {% if fieldSet.hasLegend() %}
        <legend>{{ fieldSet.getLegend()|trans }}</legend>
    {% endif %}

    {% for element in fieldSet.getAll() %}
        {{ partial(resolveView("partials/form/default/element", 'core'), ['element': element, 'combined': fieldSet.isCombined()]) }}
    {% endfor %}
</fieldset>
