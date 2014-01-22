/**
 * Form logic.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright Copyright (c) 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
(function (window, $, root, undefined) {
    $(function () {
        setup();
        root.Form.initConditions();

        function setup() {
            var form = root.ns('PhalconEye.Form');

            form.initConditions = function () {
                var comparison = {
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
                };

                var operator = {
                    'or': function (x, y) {
                        return x || y;
                    },
                    'and': function (x, y) {
                        return x && y;
                    }
                };

                var resolveCondition = function (condition) {
                    var result = true,
                        element = false,
                        currentValue = false,
                        currentComparison = false,
                        currentOperator = false;

                    $.each(condition.split(':'), function (index, item) {
                        if (!element && (!(item in comparison) && !(item in operator))) {
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
                        if (item in comparison) {
                            currentComparison = comparison[item];
                        }

                        // Check current operator.
                        if (item in operator) {
                            currentOperator = operator[item];
                        }
                    });

                    return result;
                };

                var setupEvents = function (item, condition) {
                    var element = false;

                    $.each(condition.split(':'), function (index, value) {
                        if (value in comparison || value in operator) {
                            return;
                        }

                        if (!element) {
                            element = $('#' + value, 'form');
                            if (element) {
                                element.change(function () {
                                    switchElement(item, condition);
                                });
                            }
                        }
                        else {
                            element = false;
                        }
                    });
                };

                var switchElement = function (item, condition, fast) {
                    if (item.parents('.form_element_container').length) {
                        item = item.parents('.form_element_container');
                    }

                    if (resolveCondition(condition)) {
                        item.show((fast ? 0 : 500));
                    }
                    else {
                        item.hide((fast ? 0 : 500));
                    }
                };

                $('*[data-related]', 'form').each(function (index, item) {
                    item = $(item);
                    var condition = item.data('related');
                    switchElement(item, condition, true);
                    setupEvents(item, condition);
                });
            }
        }
    });
}(window, jQuery, PhalconEye));

