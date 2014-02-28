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

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Packages management - Libraries"|i18n }}{% endblock %}

{% block head %}
    <script type="text/javascript">
        var removePackage = function (type, name) {
            if (confirm('{{ 'Are you really want to remove this package? Once removed, it can not be restored.' |i18n}}')) {
                window.location.href = '{{ url(['for':'admin-packages-uninstall', 'type':'%type%', 'name':'%name%', 'return':'admin-packages-libraries']) }}'.replace('%type%', type).replace('%name%', name);
            }
        }
    </script>
{% endblock %}

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
                    <li {% if not package.enabled %}class="disabled"{% endif %}>
                        <div class="package_info">
                            <h3>{{ package.title }} <span>v.{{ package.version }}</span></h3>

                            <div class="author">{{ package.author }}</div>
                            <div class="website"><a href="{{ package.website }}">{{ package.website }}</a>
                            </div>
                            <div class="description">{{ package.description }}</div>
                        </div>
                        {% if not package.is_system %}
                            <div class="package_options">
                                {{ link_to(['for':'admin-packages-edit', 'type':package.type, 'name':package.name, 'return':'admin-packages-libraries'], 'Edit' |i18n, 'class': 'btn btn-default') }}
                                {{ link_to(['for':'admin-packages-export', 'type':package.type, 'name':package.name], 'Export' |i18n, 'class': 'btn btn-default', 'data-widget':'modal') }}
                                <a class="btn btn-danger" href="javascript:;"
                                   onclick="removePackage('{{package.type}}', '{{ package.name }}');">{{ 'Uninstall' |i18n }}</a>
                            </div>
                        {% endif %}
                        <div class="clear"></div>
                    </li>
                {% endfor %}
                {% if packages.count() is 0 %}
                    <li>
                        <h2 style="text-align: center;">{{ 'No packages'|i18n }}</h2>
                    </li>
                {% endif %}
            </ul>
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
