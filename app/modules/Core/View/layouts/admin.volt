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

<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />

    {{ assets.outputCss() }}

    {{ assets.outputInline() }}

    <script type="text/javascript">
        {{ helper('i18n', 'core').render() }}
    </script>

    {% block head %}

    {% endblock %}

</head>

<body data-base-url="{{ url() }}" data-debug="{{ config.application.debug }}">

<div class="navbar navbar_panel">
    <div class="navbar-inner">
        <a class="brand" href="{{ url("admin") }}"><img alt="Phalcon Eye"
                                                        src="{{ url('assets/img/core/pe_logo_white.png') }}"/></a>

        <div class="nav-collapse">
            {{ headerNavigation.render() }}
        </div>
    </div>

    <div class="navbar-text">
        <a href="{{ url() }}" class="btn btn-primary">{{ 'Back to site' |i18n }}</a>
        <a href="{{ url("logout") }}" class="btn btn-danger">{{ 'Logout' |i18n }}</a>
    </div>
</div>

<div class="content">

    {% block header -%}
    {%- endblock %}

    <div class="row-fluid row-after-header">
        <div>
            {{ content() }}
            {{ flashSession.output() }}
        </div>
    </div>

    <div class="row-fluid main-content">
        <!--/row-->
        {%- block content -%}
        {%- endblock %}
    </div>
    <!--/row-->
</div>

<div id="footer">
    PhalconEye v.<?php echo PHALCONEYE_VERSION ?> <br/>[{{ date('d-m-Y H:i:s') }}]
</div>

{{ assets.outputJs() }}

</body>
</html>