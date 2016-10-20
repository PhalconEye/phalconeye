{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
#}

{# TOP #}
{% if "top" in (content|keys) %}
    <section class="content-top">
    {% for widget in content["top"] %}
        {{ widget }}
    {% endfor %}
    </section>
{% endif %}

{# MIDDLE #}
{% if "middle" in (content|keys) %}

    {# MIDDLE FULL #}
    {% if ("right" not in (content|keys)) and ("left" not in (content|keys)) %}
        <section class="content-full">
        {% for widget in content["middle"] %}
            {{ widget }}
        {% endfor %}
        </section>
    {% endif %}

    {# MIDDLE LEFT #}
    {% if "left" in (content|keys) %}
        <aside class="content-left">
        {% for widget in content["left"] %}
            {{ widget }}
        {% endfor %}
        </aside>
    {% endif %}

    {# MIDDLE LEFT COLUMN #}
    {% if ("right" in (content|keys)) and ("left" not in (content|keys)) %}
        <section class="content-left">
        {% for widget in content["middle"] %}
            {{ widget }}
        {% endfor %}
        </section>
    {% endif %}

    {# MIDDLE CONTENT #}
    {% if ("right" in (content|keys)) and ("left" in (content|keys)) %}
        <section class="content">
        {% for widget in content["middle"] %}
            {{ widget }}
        {% endfor %}
        </section>
    {% endif %}

    {# MIDDLE RIGHT #}
    {% if ("left" in (content|keys)) and ("right" not in (content|keys)) %}
        <section class="content-right">
            {% for widget in content["middle"] %}
                {{ widget }}
            {% endfor %}
        </section>
    {% endif %}

    {# RIGHT #}
    {% if "right" in (content|keys) %}
        <aside class="content-right">
        {% for widget in content["right"] %}
            {{ widget }}
        {% endfor %}
        </aside>
    {% endif %}

{% endif %}

{# BOTTOM #}
{% if "bottom" in (content|keys) %}
    <section class="content-bottom">
    {% for widget in content["bottom"] %}
        {{ widget }}
    {% endfor %}
    </section>
{% endif %}
