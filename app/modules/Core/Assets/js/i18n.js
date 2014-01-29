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
 * Javascript translator.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
var translatorData = translatorData || [];
(function (window, $, root, data, undefined) {
    $(function () {
        root.ns(
            'PhalconEye.i18n',
            {
                translations: data,
                /**
                 * Add translation into storage.
                 *
                 * @param key
                 * @param text
                 */
                add: function (key, text) {
                    this.translations[key] = text;
                },

                /**
                 * Translate text.
                 * Variables can be used for string substitution.
                 *
                 * @param key
                 * @param variables
                 */
                _: function (key, variables) {
                    var translation = this.translations[key];
                    if (translation != undefined) {
                        return this._format(translation, variables);
                    }

                    return key;
                },

                _format: function (string, variables) {
                    if (variables != undefined) {
                        for (var i = 0; i < variables.length; i++) {
                            var regexp = new RegExp('\\{' + i + '\\}', 'gi');
                            string = string.replace(regexp, variables[i]);
                        }
                    }
                    return string;
                }
            }
        );
    });
}(window, jQuery, PhalconEye, translatorData));

