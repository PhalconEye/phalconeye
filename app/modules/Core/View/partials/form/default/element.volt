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
{% if instanceof(element, 'Engine\Form\FieldSet') %}
    {{ partial(resolveView("partials/form/default/fieldSet", 'core'), ['fieldSet': element]) }}
{% else %}
    {% if element.useDefaultLayout() %}

        {% if combined is not defined or not combined %}
            <div class="form_element_container{% if form.hasErrors(element.getName()) %} validation_failed{% endif %}">
        {% endif %}

        {% if element.getOption('label') or element.getOption('description') %}
            <div class="form_label">
                {% if element.getOption('label') %}
                    <label for="{{ element.getName() }}">
                        {{ element.getOption('label') |trans }}
                        {% if element.getOption('required') %}
                            *
                        {% endif %}
                    </label>
                {% endif %}
                {% if element.getOption('description') %}
                    <p>{{ element.getOption('description') |trans }}</p>
                {% endif %}
            </div>
        {% endif %}
        <div class="form_element">
            {% if instanceof(element, 'Engine\Form\Element\File') and element.getOption('isImage') %}
                <div class="form_element_file_image">
                    <img alt="" src="{{ element.getValue() }}"/>
                </div>
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
