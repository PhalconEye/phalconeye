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

{{ partial("window", ['name':'config', 'title':'Phalcon Eye Config', 'content':htmlConfig]) }}
{{ partial("window", ['name':'router', 'title':'Router', 'content':htmlRouter]) }}
{{ partial("window", ['name':'memory', 'title':'Memory', 'content':htmlMemory]) }}
{{ partial("window", ['name':'time', 'title':'Time', 'content':htmlTime]) }}
{{ partial("window", ['name':'files', 'title':'Files', 'content':htmlFiles]) }}
{{ partial("window", ['name':'sql', 'title':'SQL', 'content':htmlSql]) }}
{{ partial("window", ['name':'errors', 'title':'Errors', 'content':htmlErrors]) }}

<div class="profiler">
    <div data-window="config" class="item"><img alt="Phalcon Eye Profiler" src="/favicon.ico"/></div>
    <div data-window="router" class="item">{{ handlerValues['router'] }}</div>
    <div data-window="memory" class="item item-right item-memory {{ handlerValues['memory']['class'] }}">{{ handlerValues['memory']['value'] }}
        kb
    </div>
    <div data-window="time" class="item item-right item-time {{ handlerValues['time']['class'] }}">{{ handlerValues['time']['value'] }}
        ms
    </div>
    <div data-window="files" class="item item-right item-files">{{ handlerValues['files'] }}</div>
    <div data-window="sql" class="item item-right item-sql">{{ handlerValues['sql'] }}</div>
    <div data-window="errors" class="item item-right item-errors {{ handlerValues['errors']['class'] }}">{{ handlerValues['errors']['value'] }}</div>
</div>