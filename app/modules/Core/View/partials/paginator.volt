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

{% if paginator.total_pages > 1 %}
    {% set startIndex = 1 %}

    {% if paginator.total_pages > 10 %}
        {% if paginator.current > 4 %}
            {% set startIndex = startIndex + paginator.current - 4 %}
        {% endif %}
        {% if paginator.total_pages - paginator.current < 10 %}
            {% set startIndex = paginator.total_pages - 9 %}
        {% endif %}
    {% endif %}

    <div class="pagination-container">
        <ul class="pagination">
            {% if paginator.current > 1 %}
                <li>
                    <a href="{{ helper('url').paginatorUrl() }}" data-page="1">{{ 'First' |i18n }}</a>
                </li>
                <li>
                    <a href="{{ helper('url').paginatorUrl(paginator.before) }}" data-page="{{ paginator.before }}">&laquo;</a>
                </li>
            {% endif %}

            {% for pageIndex in startIndex..paginator.total_pages %}
                {% if pageIndex is startIndex+10 %}
                    {% break %}
                {% endif %}

                <li {% if pageIndex is paginator.current %}class="active"{% endif %}>
                    <a href="{{ helper('url').paginatorUrl(pageIndex) }}" data-page="{{ pageIndex }}">{{ pageIndex }}</a>
                </li>
            {% endfor %}

            {% if paginator.current < paginator.total_pages %}
                <li>
                    <a href="{{ helper('url').paginatorUrl(paginator.current + 1) }}" data-page="{{ paginator.current + 1 }}">&raquo;</a>
                </li>
                <li>
                    <a href="{{ helper('url').paginatorUrl(paginator.last ) }}" data-page="{{ paginator.last }}">{{ 'Last' |i18n }}</a>
                </li>
            {% endif %}
        </ul>
    </div>
{% endif %}