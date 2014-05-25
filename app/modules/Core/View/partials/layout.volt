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

{# TOP #}
{% if "top" in (content|keys) %}
    <div id="general-content-full-top">
        {% for widget in content["top"] %}
            {{ widget }}
        {% endfor %}
    </div>
{% endif %}

{# LEFT #}
{% if "left" in (content|keys) %}
    <div id="general-content-left">
        {% for widget in content["left"] %}
            {{ widget }}
        {% endfor %}
    </div>
{% endif %}

{# RIGHT #}
{% if "right" in (content|keys) %}
    <div id="general-content-right">
        {% for widget in content["right"] %}
            {{ widget }}
        {% endfor %}
    </div>
{% endif %}

{# MIDDLE #}
{% if "middle" in (content|keys) %}

    {# LEFT MIDDLE RIGHT #}
    {% if ("right" in (content|keys)) and ("left" in (content|keys)) %}
        <div id="general-content">
            {% for widget in content["middle"] %}
                {{ widget }}
            {% endfor %}
        </div>
    {% endif %}
    {# MIDDLE RIGHT #}
    {% if ("right" in (content|keys)) and ("left" not in (content|keys)) %}
        <div id="general-content-column-left">
            {% for widget in content["middle"] %}
                {{ widget }}
            {% endfor %}
        </div>
    {% endif %}
    {# LEFT MIDDLE#}
    {% if ("left" in (content|keys)) and ("right" not in (content|keys)) %}
        <div id="general-content-column-right">
            {% for widget in content["middle"] %}
                {{ widget }}
            {% endfor %}
        </div>

        {# FULL MIDDLE#}
    {% endif %}

    {% if ("right" not in (content|keys)) and ("left" not in (content|keys)) %}
        <div id="general-content-full">
            {% for widget in content["middle"] %}
                {{ widget }}
            {% endfor %}
        </div>
    {% endif %}

{% endif %}

{# BOTTOM #}
{% if "bottom" in (content|keys) %}
    <div id="general-content-full-bottom">
        {% for widget in content["bottom"] %}
            {{ widget }}
        {% endfor %}
    </div>
{% endif %}