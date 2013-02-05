{# admin.volt #}
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/admin.css"/>

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
            <a class="brand" href="/admin">Phalcon Eye</a>

            <div class="nav-collapse collapse">
                <p class="navbar-text pull-right">
                    {{ 'Logged in as ' | trans }} Username
                    [<a href="/" class="navbar-link">{{ 'Back to site' | trans }}</a>]
                    {{ ' or '|trans }}
                    [<a href="/logout" class="navbar-link">{{ 'Exit' | trans }}</a>]
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
    </div><!--/row-->

    <div class="row wrapper">
        {% block content %}
        {% endblock %}
    </div><!--/row-->
</div>

<div id="footer" class="container">
     Phalcon Eye - {{ date('d-m-Y H:i:s') }}
</div>

{{ javascript_include("js/jquery/jquery-1.8.3.min.js") }}
{{ javascript_include("js/jquery/jquery-ui-1.9.0.custom.min.js") }}
{{ javascript_include("js/bootstrap/bootstrap.min.js") }}
{{ javascript_include("js/admin.js") }}
{{ javascript_include("js/modal.js") }}

</body>
</html>