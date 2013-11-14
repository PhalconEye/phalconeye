/**
 * Performance form.
 *
 * @category  PhalconEye
 * @package   PhalconEye Admin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, undefined) {
    $(function () {
        checkAdapter();
        $('#adapter').change(function () {
            checkAdapter();
        });

        function hideOptions() {
            $('#cacheDir').closest('.form_element_container').hide();
            $('#host').closest('.form_element_container').hide();
            $('#port').closest('.form_element_container').hide();
            $('#persistent').closest('.form_element_container').hide();
            $('#server').closest('.form_element_container').hide();
            $('#db').closest('.form_element_container').hide();
            $('#collection').closest('.form_element_container').hide();
        }

        function fileOptions() {
            $('#cacheDir').closest('.form_element_container').show();
        }

        function memcachedOptions() {
            $('#host').closest('.form_element_container').show();
            $('#port').closest('.form_element_container').show();
            $('#persistent').closest('.form_element_container').show();
        }

        function mongoOptions() {
            $('#server').closest('.form_element_container').show();
            $('#db').closest('.form_element_container').show();
            $('#collection').closest('.form_element_container').show();
        }

        function checkAdapter() {
            var value = $('#adapter').val();
            hideOptions();
            switch (value) {
                case '0':
                    fileOptions();
                    break;
                case '1':
                    memcachedOptions();
                    break;
                case '3':
                    mongoOptions();
                    break;
            }
        }
    });
}(window, jQuery));