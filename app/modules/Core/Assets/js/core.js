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
 * Main javascript.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
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
     * @param path Namespace path.
     * @param object Namespace object. (optional, default: {})
     * @param root Namespace root. (optional, default: window)
     *
     * @returns {*}
     */
    var ns = function (path, object, root) {
        var parts = path.split('.')
            , len = parts.length
            , i = 0
            , def = {};

        object || (object = {});
        root || (root = window);

        for (; i < len; i++) {
            if (i == len - 1) {
                def = object;
            }
            root = root[parts[i]] || (root[parts[i]] = def)
        }

        return root;
    };

    var PhalconEye = ns('PhalconEye');
    PhalconEye.ns = ns;
    PhalconEye.debug = $('body').data('debug');
    PhalconEye.baseUrl = function (path) {
        return $('body').data('baseUrl') + path;
    };
    CKEDITOR_BASEPATH = PhalconEye.baseUrl('external/ckeditor/');

    //////////////////////////
    // Public methods.
    //////////////////////////
    PhalconEye.core = {
        /**
         * Show loading image.
         */
        showLoadingStage: function () {
            if ($('#loading_stage').length) {
                return;
            }
            var bg = $('<div id="loading_stage" class="loading_stage"><span></span></div>');
            $(window.document.body).append(bg);
        },

        /**
         * Hide loading image.
         */
        hideLoadingStage: function () {
            if ($('#loading_stage').length) {
                $('.loading_stage').remove();
            }
        }
    };

    //////////////////////////
    // Initalization.
    //////////////////////////
    _initHelpers();
    _initWidgets();
    $(function () {
        _initSystem()
    });

    //////////////////////////
    // Private methods.
    //////////////////////////
    /**
     * Init helpers handler.
     *
     * @private
     */
    function _initHelpers() {
        var helper = ns('PhalconEye.helper');

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
            if (!PhalconEye.debug) {
                return;
            }

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

        /**
         * Get url params.
         * If no param provided - window.location.search will be used.
         *
         * @param url Get params from url.
         * @returns {*}
         */
        helper.getUrlParams = function (url) {
            if (!url) {
                url = window.location.search;
            }

            var re = /([^&=]+)=?([^&]*)/g,
                decode = function (str) {
                    return decodeURIComponent(str.replace(/\+/g, ' '));
                };

            var params = {}, e;
            if (url) {
                if (url.substr(0, 1) == '?') {
                    url = url.substr(1);
                }

                while (e = re.exec(url)) {
                    var k = decode(e[1]);
                    var v = decode(e[2]);
                    if (params[k] !== undefined) {
                        if (!$.isArray(params[k])) {
                            params[k] = [params[k]];
                        }
                        params[k].push(v);
                    } else {
                        params[k] = v;
                    }
                }
            }
            return params;
        }
    }

    /**
     * Init widgets system.
     *
     * @private
     */
    function _initWidgets() {
        var widget = ns('PhalconEye.widget');

        /**
         * Init widgets.
         *
         * @param context
         */
        widget.init = function (context) {
            /**
             * Look for all widgets.
             */
            $('[data-widget]', context).each(function () {
                var widgets = this.getAttribute('data-widget').split(/\s?,\s?/)
                    , len = widgets.length
                    , widget
                    , data;

                for (; len--;) {
                    widget = widgets[len];
                    if (widget.indexOf('invoked') != -1) {
                        continue;
                    }

                    if (!(widget in PhalconEye.widget)) {
                        PhalconEye.helper.log('Widget with name "{name}" not found.', {name: widget});
                        continue;
                    }

                    widget = PhalconEye.widget[widget];
                    if (widget.init) {
                        widget.init($(this));
                        widgets[len] = '(' + widgets[len] + '):invoked';
                    }
                }

                this.setAttribute('data-widget', widgets.join(', '))
            });
        }
    }

    /**
     * Init all related systems.
     *
     * @private
     */
    function _initSystem() {
        PhalconEye.widget.init();
        PhalconEye.form.init();

        /**
         * Tooltips.
         */
        $.widget("ui.tooltip", $.ui.tooltip, {
            options: {
                content: function () {
                    return $(this).prop('title');
                }
            }
        });

        $(".system-tooltip").each(function () {
            var item = $(this),
                position = item.data('tooltip-position'),
                sign = '+',
                value = '50';

            if (!position) {
                position = 'top';
            }

            if (position == 'left' || position == 'top') {
                sign = '-';
            }

            if (position == 'top' || position == 'bottom') {
                value = '20';
            }

            item.tooltip({
                position: {
                    my: "center",
                    at: position + sign + value,
                    track: false
                }
            });
        });

        /**
         * Init on update.
         */
        $(document).bind("DOMNodeInserted", function (event) {
            PhalconEye.widget.init(event.target);
            PhalconEye.form.init(event.target);
        });
    }
}(window, jQuery));

