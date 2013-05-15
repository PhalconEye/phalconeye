{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ "Packages management - Modules"|trans }}{% endblock %}


{% block head %}
    <script type="text/javascript">
        var removePackage = function (type, name) {
            if (confirm('{{ 'Are you really want to remove this package? Once removed, it can not be restored.' | trans}}')){
                window.location.href = '{{ url(['for':'admin-packages-uninstall', 'type':'%type%', 'name':'%name%', 'return':'admin-packages']) }}'.replace('%type%', type).replace('%name%', name);
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
                    <li {% if not package.isEnabled() %}class="disabled"{% endif %}>
                        <div class="package_info">
                            <h3>{{ package.getTitle() }} <span>v.{{ package.getVersion() }}</span></h3>

                            <div class="author">{{ package.getAuthor() }}</div>
                            <div class="website"><a href="{{ package.getWebsite() }}">{{ package.getWebsite() }}</a>
                            </div>
                            <div class="description">{{ package.getDescription() }}</div>
                        </div>
                        {% if not package.isSystem() %}
                        <div class="package_options">
                            {{ link_to(['for':'admin-packages-edit', 'type':package.getType(), 'name':package.getName(), 'return':'admin-packages'], 'Edit' | trans, 'class': 'btn btn-inverse') }}
                            {{ link_to(['for':'admin-packages-export', 'type':package.getType(), 'name':package.getName()], 'Export' | trans, 'class': 'btn btn-inverse', 'data-toggle':'modal') }}
                            {% if package.isEnabled() %}
                                {{ link_to(['for':'admin-packages-disable', 'type':package.getType(), 'name':package.getName(), 'return':'admin-packages'], 'Disable' | trans, 'class': 'btn btn-warning') }}
                            {% else %}
                                {{ link_to(['for':'admin-packages-enable', 'type':package.getType(), 'name':package.getName(), 'return':'admin-packages'], 'Enable' | trans, 'class': 'btn btn-success') }}
                            {% endif %}
                            <a class="btn btn-danger" href="javascript:;" onclick="removePackage('{{package.getType()}}', '{{ package.getName() }}');">{{ 'Uninstall' | trans }}</a>
                        </div>
                        {% endif %}
                        <div class="clear"></div>
                    </li>
                {% endfor %}
                {% if packages.count() is 0 %}
                <li>
                    <h2 style="text-align: center;">{{ 'No packages'|trans }}</h2>
                </li>
                {% endif %}
            </ul>
        </div>
        <!--/row-->
    </div><!--/span-->
{% endblock %}
