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

<!DOCTYPE html>
<html>
<head>
    <title>PhalconEye | {% block title %}{% endblock %}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="PhalconEye - Open Source Content Management System"/>

    {{ assets.outputCss() }}

    {{ assets.outputJs() }}


    {%- block head -%}

    {%- endblock -%}

</head>
<body>
<div id="wrapper">

    <div id="header">
        {%- block header -%}
        {%- endblock -%}
    </div>

    <div class="system-container">
        {{ content() |i18n }}
    </div>

    <div class="container">
        {%- block content -%}
        {%- endblock -%}
    </div>

    <div id="footer">
        {%- block footer -%}
        {%- endblock -%}
    </div>
</div>

</body>
</html>