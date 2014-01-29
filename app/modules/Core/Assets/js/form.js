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
 * Form logic.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    root.ns(
        'PhalconEye.form',
        {
            /**
             * Comparison definition.
             */
            _comparison: {
                '>': function (x, y) {
                    return x > y
                },
                '<': function (x, y) {
                    return x < y
                },
                '>=': function (x, y) {
                    return x >= y
                },
                '<=': function (x, y) {
                    return x <= y
                },
                '==': function (x, y) {
                    return x == y
                },
                '!=': function (x, y) {
                    return x != y
                }
            },

            /**
             * Operators definition.
             */
            _operator: {
                'or': function (x, y) {
                    return x || y;
                },
                'and': function (x, y) {
                    return x && y;
                }
            },

            /**
             * Init forms.
             *
             * @param context
             */
            init: function (context) {
                $this = this;
                if (!context) {
                    context = 'form';
                }

                $('*[data-related]', context).each(function (index, item) {
                    item = $(item);
                    var condition = item.data('related');
                    $this._switchElement(item, condition, true);
                    $this._setupEvents(item, condition);
                });
            },

            /**
             * Switch element visibility.
             *
             * @param item Element object.
             * @param condition Condition to switch.
             * @param fast How fast?
             *
             * @private
             */
            _switchElement: function (item, condition, fast) {
                if (item.parents('.form_element_container').length) {
                    item = item.parents('.form_element_container');
                }

                if (this._resolveCondition(condition)) {
                    item.show((fast ? 0 : 500));
                }
                else {
                    item.hide((fast ? 0 : 500));
                }
            },

            /**
             * Setup some events.
             *
             * @param item Element object.
             * @param condition Condition to switch.
             *
             * @private
             */
            _setupEvents: function (item, condition) {
                var element = false,
                    $this = this;

                $.each(condition.split(':'), function (index, value) {
                    if (value in $this._comparison || value in $this._operator) {
                        return;
                    }

                    if (!element) {
                        element = $('#' + value, 'form');
                        if (element) {
                            element.change(function () {
                                $this._switchElement(item, condition);
                            });
                        }
                    }
                    else {
                        element = false;
                    }
                });
            },

            /**
             * Resolve condition.
             *
             * @param condition Element condition.
             *
             * @private
             * @returns bool
             */
            _resolveCondition: function (condition) {
                var result = true,
                    element = false,
                    currentValue = false,
                    currentComparison = false,
                    currentOperator = false,
                    $this = this;

                $.each(condition.split(':'), function (index, item) {
                    if (!element && (!(item in $this._comparison) && !(item in $this._operator))) {
                        element = $('#' + item, 'form');
                    }

                    if (element && !currentValue) {
                        currentValue = element.val();
                    }

                    // Everything ready to get comparison.
                    if (currentComparison) {
                        if (currentOperator) {
                            result = currentOperator(result, currentComparison(currentValue, item));
                        }
                        else {
                            result = currentComparison(currentValue, item);
                        }
                        element = currentValue = currentComparison = currentOperator = false;
                    }

                    // Check current comparison type.
                    if (item in $this._comparison) {
                        currentComparison = $this._comparison[item];
                    }

                    // Check current operator.
                    if (item in $this._operator) {
                        currentOperator = $this._operator[item];
                    }
                });

                return result;
            }
        }
    );
}(window, jQuery, PhalconEye));

