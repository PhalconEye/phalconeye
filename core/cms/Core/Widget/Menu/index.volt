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

{% extends "Core/View/layouts/widget.volt" %}

{% block content %}
    <nav id="{{ 'nav-' ~ navigation.getId() }}" class="navbar">
        <input type="checkbox" class="navbar-toggle"  id="{{ 'nav-toggle-' ~ navigation.getId() }}" />
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
        <label class="navbar-handle" for="{{ 'nav-toggle-' ~ navigation.getId() }}"></label>
    </nav>
{% endblock %}
