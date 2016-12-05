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

{# <header> #}
{# -------- #}
{#|        |#}
{# -------- #}
{#|        |#}
{#|        |#}
{#|        |#}
{# -------- #}
{# <footer> #}

{# TOP #}
<section class="content-top">
    {% for widget in content["top"] %}
        {{ widget }}
    {% endfor %}
</section>

{# MIDDLE #}
<section class="content-full">
    {% for widget in content["middle"] %}
        {{ widget }}
    {% endfor %}
</section>