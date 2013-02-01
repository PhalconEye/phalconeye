{% if page.total_pages > 1 %}
    {% set startIndex = 1 %}

    {% if page.total_pages > 10 %}
        {% if page.current > 4 %}
            {% set startIndex = startIndex + page.current - 4 %}
        {% endif %}
        {% if page.total_pages - page.current < 10 %}
            {% set startIndex = page.total_pages - 9 %}
        {% endif %}
    {% endif %}

    <ul class="pagination">
        {% if page.current > 1 %}
            <li class="pagination_prev">
                <a href="{{ helper('currentUrl') }}">|<</a>
                <a href="{{ helper('currentUrl') }}?page={{ page.before }}"><<</a>
            </li>
        {% endif %}
        <li class="pagination_content">
            {% for pageIndex in startIndex..page.total_pages %}
                {% if pageIndex is startIndex+10 %}
                    {% break %}
                {% endif %}

                <a {% if pageIndex is page.current %}class="active"{% endif %}
                   href="{{ helper('currentUrl') }}?page={{ pageIndex }}">{{ pageIndex }}</a>
            {% endfor %}
        </li>
        {% if page.current < page.total_pages %}
            <li class="pagination_next">
                <a href="{{ helper('currentUrl') }}?page={{ page.current + 1 }}">>></a>
                <a href="{{ helper('currentUrl') }}?page={{ page.last }}">>|</a>
            </li>
        {% endif %}
    </ul>
{% endif %}