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
var PE = PE || {};
(function (window, $, root, undefined) {
    $(function () {
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
        root.ns = function (path, root) {
            var parts = path.split('\\')
                , len = parts.length
                , i = 0;

            root || (root = window);

            for (; i < len; i++) {
                root = root[parts[i]] || (root[parts[i]] = {})
            }

            return root;
        };

        //////////////////////////
        // Private methods.
        //////////////////////////

        // ... will be here.
    });
}(window, jQuery, PE));

