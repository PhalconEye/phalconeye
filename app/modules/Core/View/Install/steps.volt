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

<ul class="steps">
     <li {% if action is 'index' %}class="active"{% endif %}><a href="{{ url('install') }}">{{ 'Requirements check'|i18n }}</a></li>
     <li class="delimiter">></li>
     <li {% if action is 'database' %}class="active"{% endif %}><a href="{{ url('install/database') }} ">{{ 'Database installation'|i18n }}</a></li>
     <li class="delimiter">></li>
     <li {% if action is 'finish' %}class="active"{% endif %}><a href="{{ url('install/finish') }} ">{{ 'Final stage'|i18n }}</a></li>
 </ul>