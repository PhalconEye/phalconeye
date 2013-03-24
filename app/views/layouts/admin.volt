{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to lantian.ivan@gmail.com so we can send you a copy immediately.
#}

{# admin.volt #}
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/admin.css"/>
    {{ javascript_include("js/jquery/jquery-1.8.3.min.js") }}
    {{ javascript_include("js/jquery/jquery-ui-1.9.0.custom.min.js") }}
    {{ javascript_include("js/bootstrap/bootstrap.min.js") }}
    {{ javascript_include("js/ckeditor/ckeditor.js") }}
    {{ javascript_include("js/phalconeye/core.js") }}
    {{ javascript_include("js/phalconeye/admin.js") }}
    {{ javascript_include("js/phalconeye/modal.js") }}

    {% block head %}

    {% endblock %}

</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="{{ url("admin")}}"><img alt="Pahlcon Eye" src="/public/img/phalconeye/PE_logo_white.png"/></a>

            <div class="nav-collapse collapse">
                <p class="navbar-text pull-right">
                    {{ 'Logged in as ' | trans }} Username
                    [<a href="{{ url()}}" class="navbar-link">{{ 'Back to site' | trans }}</a>]
                    {{ ' or '|trans }}
                    [<a href="{{ url("logout")}}" class="navbar-link">{{ 'Exit' | trans }}</a>]
                </p>
                {{ headerNavigation.render() }}
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        {{ content() }}
        {{ flashSession.output() }}
    </div><!--/row-->

    <div class="row wrapper">
        {% block content %}
        {% endblock %}
    </div><!--/row-->
</div>

<div id="footer" class="container">
     Phalcon Eye - {{ date('d-m-Y H:i:s') }}
</div>

</body>
</html>