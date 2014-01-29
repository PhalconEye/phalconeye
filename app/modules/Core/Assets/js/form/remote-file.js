/**
 * Files ajax popup support.
 *
 * @category  PhalconEye
 * @package   PhalconEye Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    $(function () {
        root.ns(
            'PhalconEye.ajaxplorer',
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
                openAjaxplorerPopup: function (element, url, title) {
                    this._currentElement = element;
                    window.open(url, title, 'width=800,height=600,resizable=yes,scrollbars=yes,status=yes').focus();
                },

                /**
                 * Ajaxplorer callback.
                 *
                 * @param data Ajaxplorer data.
                 */
                ajaxplorerPopupCallback: function (data) {
                    if (typeof(data) === "string" && this._currentElement) {
                        this._currentElement.find('input[type="text"]').val(data);
                    }
                    this._currentElement = null;
                }
            }
        );
    });
}(window, jQuery, PhalconEye));

