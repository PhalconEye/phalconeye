/**
 * Main javascript.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, undefined) {
    //////////////////////////
    // Initializations.
    //////////////////////////
    /**
     * Create namespace for path.
     *
     * @param path
     * @param root
     * @returns {*}
     */
    var ns = function (path, root) {
        var parts = path.split('.')
            , len = parts.length
            , i = 0;

        root || (root = window);

        for (; i < len; i++) {
            root = root[parts[i]] || (root[parts[i]] = {})
        }

        return root;
    };

    var PhalconEye = ns('PhalconEye');
    PhalconEye.ns = ns;
    PhalconEye.baseUrl = function (path) {
        return $('body').data('baseUrl') + path;
    };

    initHelpers();

    //////////////////////////
    // Private methods.
    //////////////////////////
    /**
     * Init helpers handler.
     *
     * @private
     */
    function initHelpers() {
        var helper = ns('PhalconEye.Helper');

        /**
         * template
         *
         * util.template("Hello {name}", { name: 'world' });
         *
         * @param template
         * @param params
         * @returns {*}
         */
        helper.template = function (template, params) {
            var item, i;
            for (i in params) {
                item = '{' + i + '}';
                if (params.hasOwnProperty(i) && !!~template.indexOf(item)) {
                    template = template.replace(new RegExp(item, 'g'), params[i])
                }
            }
            return template;
        };

        /**
         * Log message.
         *
         * @param msg
         * @param params
         */
        helper.log = function (msg, params) {
            console.error(this.template(msg, params));
        };

        /**
         * Scroll to selector or element.
         *
         * @param el selector|jquery
         */
        helper.scrollTo = function (el) {
            if (!(el instanceof  $))  el = $(el);
            $('html, body').animate({scrollTop: el.offset().top - 50}, 2000);
        };
    }

}(window, jQuery));

