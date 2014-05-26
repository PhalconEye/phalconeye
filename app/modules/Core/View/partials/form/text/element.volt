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
        <div class="form_element">
            {% if instanceof(element, 'Engine\Form\Element\File') and element.getOption('isImage') and element.getValue() != '/' %}
                <div class="form_element_file_image">
                    <img alt="" src="{{ element.getValue() }}"/>
                </div>
            {% endif %}
            {{ element.getValue() }}
        </div>

        {% if combined is not defined or not combined %}
            </div>
        {% endif %}

    {% else %}
        {% if instanceof(element, 'Engine\Form\Element\Button') or instanceof(element, 'Engine\Form\Element\ButtonLink') %}
            {{ element.render() }}
        {% else %}
            {{ element.getValue() }}
        {% endif %}
    {% endif %}
{% endif %}
