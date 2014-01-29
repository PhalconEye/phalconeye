/**
 * CkEditor widget.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    root.ns(
        'PhalconEye.widget.ckeditor',
        {
            /**
             * Init ckeditor.
             *
             * @param element Ckeditor element.
             */
            init: function (element) {
                var data = element.data('options');
                if (typeof data == 'string') {
                    data = $.parseJSON(data);
                }
                else {
                    data = {};
                }

                CKEDITOR.replace(element.data('name'), data);
            }
        }
    );
}(window, jQuery, PhalconEye));
