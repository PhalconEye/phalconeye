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

<fieldset{{ fieldSet.renderAttributes() }}>
    {% if fieldSet.hasLegend() %}
        <legend>{{ fieldSet.getLegend()|i18n }}</legend>
    {% endif %}

    {% for element in fieldSet.getAll() %}
        {{ partial(form.getElementView(), ['element': element, 'combined': fieldSet.isCombined()]) }}
    {% endfor %}
</fieldset>
