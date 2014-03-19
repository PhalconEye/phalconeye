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

<script type="application/javascript">
    var addNewEvent = function () {
        var clone = $('.events-row:first').clone();
        $('input', clone).val('');
        clone.appendTo('.events-container');
    }

    var removeEvent = function(item){
        item.closest('.events-row').remove();
    }
</script>
<div>
    <a href="javascript:;" class="btn btn-info" onclick="addNewEvent();">{{ 'Add new'|i18n }}</a>
</div>
<div class="events-container">
    {% if events is not defined or events is empty %}
    <div class="events-row">
        <div class="events-event">
            {{ 'Event'|i18n }}:
            <input type="text" required="required" name="event[]">
        </div>
        <div class="events-class">
            {{ 'Class'|i18n }}:
            <input type="text" required="required" name="class[]">
        </div>
    </div>
    {% else %}
        {% for key, event in events['event'] %}
            <div class="events-row">
                <div class="events-event">
                    {{ 'Event'|i18n }}:
                    <input type="text" required="required" name="event[]" value="{{ event }}">
                </div>
                <div class="events-class">
                    {{ 'Class'|i18n }}:
                    <input type="text" required="required" name="class[]" value="{{ events['class'][key] }}">
                </div>
                <div class="events-remove" onclick="removeEvent($(this));">x</div>
            </div>
        {% endfor %}
    {% endif %}
</div>