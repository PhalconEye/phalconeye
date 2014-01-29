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
 * Profiler logic.
 *
 * @category  PhalconEye
 * @package   PhalconEye Profiler
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, undefined) {
    $(function () {
        function hideWindow() {
            $('.profiler .item').removeClass('active');
            $('.profiler_window').hide();
        }

        function setCookie(name, value, exp_y, exp_m, exp_d, path, domain, secure) {
            var cookie_string = name + "=" + escape(value);
            if (exp_y) {
                var expires = new Date(exp_y, exp_m, exp_d);
                cookie_string += "; expires=" + expires.toGMTString();
            }
            if (path)
                cookie_string += "; path=" + escape(path);

            if (domain)
                cookie_string += "; domain=" + escape(domain);

            if (secure)
                cookie_string += "; secure";

            document.cookie = cookie_string;
        }

        function deleteCookie(cookie_name) {
            var cookie_date = new Date();
            cookie_date.setTime(cookie_date.getTime() - 1);
            document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
        }

        function getCookie(cookie_name) {
            var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');

            if (results)
                return ( unescape(results[2]) );
            else
                return null;
        }

        var profilerWindow = getCookie('profiler_window');
        if (profilerWindow && $(profilerWindow)) {
            $(profilerWindow).show();
        }

        $('.profiler .item').click(function () {
            console.log(2);
            var id = '#profiler_window_' + $(this).data('window');
            if ($(id)) {
                hideWindow();

                $(this).addClass('active');
                $(id).show();
                setCookie('profiler_window', id);
            }
        });

        $('.profiler_window_close').click(function () {
            hideWindow();
            deleteCookie('profiler_window');
        });
    });
}(window, jQuery));

