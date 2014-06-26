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

    <{{ navigation.getOption('listItemTag') }}{% for name,value in navigation.getItemAttributes(item) %} {{ name }}="{{ value }}"{% endfor %}>
        <a{% for name,value in navigation.getLinkAttributes(item) %} {{ name }}="{{ value }}"{% endfor %}>
            {{
                item.getOption('prepend') ~
                item.getOption('itemPrependContent') ~
                item.getLabel()|i18n ~
                item.getOption('itemAppendContent') ~
                item.getOption('append')
            }}</a>

        {% if item|length %}
        <{{ navigation.getOption('listTag') }} class="{{ navigation.getOption('dropDownItemMenuClass') }}">
            {% for item in item.getItems() %}
            {{ partial('Core/View/partials/navigation/item', ['item': item, 'navigation': navigation]) }}
            {% endfor %}
        </{{ navigation.getOption('listTag') }}>
        {% endif %}
    </{{ navigation.getOption('listItemTag') }}>