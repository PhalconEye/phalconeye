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

<ul class="navigation">
     <li {% if action is 'license' %}class="active"{% endif %}>
         <a href="{{ url('install') }}">
             <span class="glyphicon glyphicon-file"></span>
             <p>{{ 'License'|i18n }}</p>
         </a>
     </li>
     <li {% if action is 'requirements' %}class="active"{% endif %}>
         <a href="{{ url('install/requirements') }}">
             <span class="glyphicon glyphicon-list"></span>
             <p>{{ 'Requirements'|i18n }}</p>
         </a>
     </li>
     <li {% if action is 'database' %}class="active"{% endif %}>
         <a href="{{ url('install/database') }} ">
             <span class="glyphicon glyphicon-hdd"></span>
             <p>{{ 'Database'|i18n }}</p>
         </a>
     </li>
     <li {% if action is 'finish' %}class="active"{% endif %}>
         <a href="{{ url('install/finish') }} ">
             <span class="glyphicon glyphicon-ok"></span>
             <p>{{ 'Final'|i18n }}</p>
         </a>
     </li>
</ul>

<div class="header_container">
    <img alt="" src="{{ url('assets/img/core/pe_logo.png') }}"/>
    <div>
        v.<?php echo PHALCONEYE_VERSION ?> {{ 'installation...'|i18n }}
    </div>
</div>