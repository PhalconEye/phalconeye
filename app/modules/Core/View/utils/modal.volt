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

<script type="text/javascript">
    var hideModal = function () {
        PhalconEye.widget.modal.hide();
        {% if reload is defined %}
        window.location.reload();
        {% endif %}
    };

    {% if hide is defined %}

    var hideTimeout = parseInt('{{ hide }}');
    if (hideTimeout) {
        setTimeout(hideModal, hideTimeout);
    }
    else {
        hideModal();
    }

    {% elseif reload is defined %}

    var reloadTimeout = parseInt('{{ reload }}');
    if (reloadTimeout) {
        setTimeout(function () {
            window.location.reload();
        }, reloadTimeout);
    }
    else {
        window.location.reload();
    }

    {% endif %}

    {% if customJs is defined %}
    {{ customJs }}
    {% endif %}
</script>


{% block body %}
    {% if message is defined %}
        <div class="modal-message">
            {{ message|i18n }}
        </div>
    {% endif %}
{% endblock %}