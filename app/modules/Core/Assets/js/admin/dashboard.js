/*
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
 */

/**
 * Dashboard scripts.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

(function (window, $, root, undefined) {
    $(function () {
        var element = $('[name="debug"]');

        element.bootstrapSwitch('size', 'large');
        element.on('switchChange', function (e, data) {
            var $this = $(data.el),
                value = data.value;

            root.core.showLoadingStage();

            $.ajax({
                type: "GET",
                url: $this.data('href'),
                data: {
                    'debug': value ? 1 : 0
                },
                dataType: 'json'
            }).always(function() {
                root.core.hideLoadingStage();
            });

        });
    });
}(window, jQuery, PhalconEye));

