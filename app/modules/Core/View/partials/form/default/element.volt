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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
#}

{% if instanceof(element, 'Engine\Form\FieldSet') %}
    {{ partial(form.getFieldSetView(), ['fieldSet': element]) }}
{% else %}
    {% if element.useDefaultLayout() %}

        {% if combined is not defined or not combined %}
            <div class="form_element_container{% if form.hasErrors(element.getName()) %} validation_failed{% endif %}">
        {% endif %}

        {% if element.getOption('label') or element.getOption('description') %}
            <div class="form_label">
                {% if element.getOption('label') %}
                    <label for="{{ element.getName() }}">
                        {{ element.getOption('label') |i18n }}
                        {% if element.getOption('required') %}
                            *
                        {% endif %}
                    </label>
                {% endif %}
                {% if element.getOption('description') %}
                    <p>{{ element.getOption('description') |i18n }}</p>
                {% endif %}
            </div>
        {% endif %}

        {% if element.isDynamic() %}
        <div class="form_element"
             data-dynamic="{{ element.getName() }}"
             data-dynamic-min="{{ element.getOption('dynamic')['min'] }}"
             data-dynamic-max="{{ element.getOption('dynamic')['max'] }}">
        {% else %}
        <div class="form_element">
        {% endif %}
            {{ element.render() }}
        </div>

        {% if combined is not defined or not combined %}
            </div>
        {% endif %}

    {% else %}
        {{ element.render() }}
    {% endif %}
{% endif %}
