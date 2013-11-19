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
        root.ajaxplorer = {
            currentElement: null,

            openAjaxplorerPopup: function (element, url, title) {
                currentElement = element;
                window.open(url, title, 'width=800,height=600,resizable=yes,scrollbars=yes,status=yes').focus();
            },

            ajaxplorerPopupCallback: function (data) {
                if (typeof(data) === "string" && currentElement) {
                    currentElement.find('input[type="text"]').val(data);
                }
                currentElement = null;
            }
        }
    });
}(window, jQuery, PE));

