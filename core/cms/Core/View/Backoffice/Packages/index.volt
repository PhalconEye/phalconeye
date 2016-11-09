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
  +------------------------------------------------------------------------+
#}

{% extends "Core/View/layouts/backoffice.volt" %}

{% block title %}{{ "Loaded Packages - Modules"|i18n }}{% endblock %}

{% block header %}
    <div class="navbar navbar-header">
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
    </div>
{% endblock %}

{% block content %}
    <div class="span12">
        <div class="row-fluid">
            <ul class="package_list">
                {% for package in packages %}
                    <li {% if package.isDisabled() %}class="disabled"{% endif %}>
                        <div class="package_info">
                            <h3>{{ package.getName() }} <span>v.{{ package.getMetadata('version') }}</span></h3>

                            <div class="author">{{ package.getMetadata('author')|e }}</div>
                            <div class="website"><a href="{{ package.getMetadata('website') }}">{{ package.getMetadata('website') }}</a>
                            </div>
                            <div class="description">{{ package.getMetadata('description') }}</div>
                        </div>
                        <div class="clear"></div>
                    </li>
                {% endfor %}
                {% if packages|length is 0 %}
                <li>
                    <h2 style="text-align: center;">{{ 'No packages'|i18n }}</h2>
                </li>
                {% endif %}
            </ul>
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
