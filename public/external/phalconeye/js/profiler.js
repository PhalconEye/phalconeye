/**
 * Profiler logic.
 *
 * @category  PhalconEye
 * @package   PhalconEye Profiler
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, undefined) {
    $(function () {
        function hideWindow() {
            $('.profiler .item').removeClass('active');
            $('.profiler_window').hide();
        }
console.log(1);
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
            var id = '#profiler_window_' + $(this).attr('window');
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

