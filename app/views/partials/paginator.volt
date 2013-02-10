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

    <ul class="pagination">
        {% if paginator.current > 1 %}
            <li class="pagination_prev">
                <a href="{{ helper('currentUrl') }}">|<</a>
                <a href="{{ helper('currentUrl') }}?page={{ paginator.before }}"><<</a>
            </li>
        {% endif %}
        <li class="pagination_content">
            {% for pageIndex in startIndex..paginator.total_pages %}
                {% if pageIndex is startIndex+10 %}
                    {% break %}
                {% endif %}

                <a {% if pageIndex is paginator.current %}class="active"{% endif %}
                   href="{{ helper('currentUrl') }}?page={{ pageIndex }}">{{ pageIndex }}</a>
            {% endfor %}
        </li>
        {% if paginator.current < paginator.total_pages %}
            <li class="pagination_next">
                <a href="{{ helper('currentUrl') }}?page={{ paginator.current + 1 }}">>></a>
                <a href="{{ helper('currentUrl') }}?page={{ paginator.last }}">>|</a>
            </li>
        {% endif %}
    </ul>
{% endif %}