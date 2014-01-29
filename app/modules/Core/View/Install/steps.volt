{#
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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
     <li {% if action is 'index' %}class="active"{% endif %}><a href="/install">{{ 'Requirements check'|trans }}</a></li>
     <li class="delimiter">></li>
     <li {% if action is 'database' %}class="active"{% endif %}><a href="/install/database">{{ 'Database installation'|trans }}</a></li>
     <li class="delimiter">></li>
     <li {% if action is 'finish' %}class="active"{% endif %}><a href="/install/finish">{{ 'Final stage'|trans }}</a></li>
 </ul>