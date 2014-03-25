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
 * Files ajax popup support.
 *
 * @category  PhalconEye
 * @package   PhalconEye
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    $(function () {
        root.ns(
            'PhalconEye.pydio',
            {
                /**
                 * Current element object.
                 *
                 * @private
                 */
                _currentElement: null,

                /**
                 * Init ckeditor.
                 *
                 * @param element Element object.
                 * @param url Url.
                 * @param title Button title.
                 */
                openPopup: function (element, url, title) {
                    this._currentElement = element;
                    window.open(url, title, 'width=800,height=600,resizable=yes,scrollbars=yes,status=yes').focus();
                },

                /**
                 * Ajaxplorer callback.
                 *
                 * @param data Ajaxplorer data.
                 */
                popupCallback: function (data) {
                    if (typeof(data) === "string" && this._currentElement) {
                        if (data.charAt(0) == '/'){
                            data = data.substr(1);
                        }
                        this._currentElement.find('input[type="text"]').val(data);
                    }
                    this._currentElement = null;
                }
            }
        );
    });
}(window, jQuery, PhalconEye));

