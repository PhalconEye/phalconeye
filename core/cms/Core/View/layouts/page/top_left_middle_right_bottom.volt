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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  +------------------------------------------------------------------------+
#}

{#   <header>  #}
{# ----------- #}
{#|           |#}
{# ----------- #}
{#|  |     |  |#}
{#|  |     |  |#}
{#|  |     |  |#}
{# ----------- #}
{#|           |#}
{# ----------- #}
{#   <footer>  #}

{# TOP #}
<section class="content-top">
    {% for widget in content["top"] %}
        {{ widget }}
    {% endfor %}
</section>

{# LEFT #}
<aside class="content-left">
    {% for widget in content["left"] %}
        {{ widget }}
    {% endfor %}
</aside>

{# MIDDLE #}
<section class="content">
    {% for widget in content["middle"] %}
        {{ widget }}
    {% endfor %}
</section>

{# RIGHT #}
<aside class="content-right">
    {% for widget in content["right"] %}
        {{ widget }}
    {% endfor %}
</aside>

{# BOTTOM #}
<section class="content-bottom">
    {% for widget in content["bottom"] %}
        {{ widget }}
    {% endfor %}
</section>
