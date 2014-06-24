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

{# todo: Split or move this logic off here #}

{% set class = '', subItems = item.getItems() %}

{% if subItems|length %}
    {% if nested %}
       {% set class = params['dropDownSubItemMenuClass'] %}
   {% else %}
       {% set class = params['dropDownItemClass'] %}
   {% endif %}
{% endif %}

{% if item.isActive() %}
    {% if nested %}
        {% set class = class ~ ( params['highlightActiveDropDownItem'] ? ' active' : '' ) %}
    {% else %}
        {% set class = class ~ ' active' %}
    {% endif %}
{% endif %}

    <{{ params['listItemTag'] }}{% if class %} class="{{ class }}" {% endif %}>
        <a class="system-tooltip {{ subItems|length ? params['dropDownItemToggleClass'] : '' }} {{ nested ? params['dropDownItemHeaderClass'] : '' }} {% if item.getLabel() == '' %}{{ params['dropDownItemDividerClass'] }}{% endif %}"
           {% if subItems|length %}data-toggle="dropdown"{% endif %} {% for name,value in item.buildLinkParameters() %}{{ name }}="{{ value }}" {% endfor %}>
            {{
                item.getParameter('prepend') ~
                item.getParameter('itemPrependContent') ~
                item.getLabel() ~
                item.getParameter('itemAppendContent') ~
                item.getParameter('append')
            }}</a>

        {% if subItems|length %}
        <{{ params['listTag'] }} class="{{ params['dropDownItemMenuClass'] }}">
            {% for item in subItems %}
            {{ partial('Core/View/partials/navigation/item', ['item': item, 'params': params, 'nested': true]) }}
            {% endfor %}
        </{{ params['listTag'] }}>
        {% endif %}
    </{{ params['listItemTag'] }}>