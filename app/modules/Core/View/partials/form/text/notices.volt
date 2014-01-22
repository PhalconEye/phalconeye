{#
   PhalconEye

   LICENSE

   This source file is subject to the new BSD license that is bundled
   with this package in the file LICENSE.txt.

   If you did not receive a copy of the license and are unable to
   obtain it through the world-wide-web, please send an email
   to phalconeye@gmail.com so we can send you a copy immediately.

   Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
#}

{% if form.hasNotices() %}
    <ul class="form_notices">
    {% for msg in form.getNotices() %}
        <li class="alert alert-success">{{ msg }}</li>
    {% endfor %}
    </ul>
{% endif %}