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

{% extends "layouts/modal.volt" %}

{% block title %}
    {{ 'Translations wizard'|i18n }}
{% endblock %}

{% block body %}
    {% if total is 0 %}
        {{ 'There is no translations that required to be translated.'|i18n }}
    {% else %}
        <script>
            var yandexKey = 'trnsl.1.1.20140228T082249Z.18f28b2e926280db.78011118432db6f72ba9fc87278b4d2ea032a8f0',
                    sourceText = encodeURI(document.getElementById("original").value),
                    language = '{{ constant('\Engine\Config::CONFIG_DEFAULT_LANGUAGE') }}-{{ item.language }}',
                    source = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + yandexKey + '&lang=' + language + '&callback=translateText&text=' + sourceText,
                    newScript = document.createElement('script'),
                    translateText = function (response) {
                        if (!response.text || !response.text.length) {
                            return;
                        }

                        var element = document.getElementById("suggestion");
                        element.value = response.text[0];
                        element.style.cssText = 'background: #E0E0E0 !important';
                    };


            newScript.type = 'text/javascript';
            newScript.src = source;
            document.getElementsByTagName('head')[0].appendChild(newScript);
        </script>

        <h4>{{ 'Left to translate'|i18n }}: {{ total }}</h4>
        {{ form.render() }}
    {% endif %}
{% endblock %}

{% block footer %}
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" style="float:left">{{ "Close"|i18n }}</button>
        {% if total is not 0 %}
            <button class="btn btn-primary btn-save">{{ "Next"|i18n }}</button>
        {% endif %}
    </div>
{% endblock %}
