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
 | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
 +------------------------------------------------------------------------+
 */

/**
 * Dynamic form field.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright Copyright (c) 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

(function (window, $, root) {
    root.ns(
        'PhalconEye.form.dynamicField',
        {
            /**
             * Elements
             */
            _elements: {
                controls: $('<div></div>').addClass('dynamic-controls'),
                addControl: $('<button></button>').attr('type', 'button').addClass('dynamic-add btn btn-primary'),
                delControl: $('<button></button>').attr('type', 'button').addClass('dynamic-del btn btn-danger'),
                addButton: $('<i></i>').addClass('glyphicon glyphicon-plus-sign'),
                delButton: $('<i></i>').addClass('glyphicon glyphicon-minus-sign')
            },

            /**
             * Init dynamic field.
             *
             * @param scope Element object.
             */
            init: function (scope) {

                scope = $(scope);

                var $this = this,
                    controls = scope.find('.dynamic-controls');

                // Init controls area
                if (controls.length == 0) {
                    controls = this._elements.controls.clone().appendTo(scope);
                }

                // Create Add button if possible
                if (this._canAdd(scope)) {
                    if (controls.find('.dynamic-add').length == 0) {

                        $this._elements.addControl
                            .clone()
                            .append($this._elements.addButton.clone())
                            .append((root.i18n && root.i18n._('Add')) || 'Add')
                            .prependTo(controls)
                            .on('click', function() {
                                $this.addElementTo(scope);
                            });
                    }
                } else {
                    controls.find('.dynamic-add').remove();
                }

                // Create Remove button if possible
                if (this._canRemove(scope)) {
                    if (controls.find('.dynamic-del').length == 0) {

                        $this._elements.delControl
                            .clone()
                            .append($this._elements.delButton.clone())
                            .append((root.i18n && root.i18n._('Delete')) || 'Delete')
                            .appendTo(controls)
                            .on('click', function() {
                                $this.removeElementFrom(scope);
                            });
                    }
                } else {
                    controls.find('.dynamic-del').remove();
                }
            },

            /**
             * Clone element
             *
             * @param element Element object
             *
             * @return Cloned element object
             */
            cloneElement: function(element) {

                var clone = null,
                    realElement = null;

                // Clone last element
                if (element.parent().hasClass('form_element_remote_file')) {
                    clone = element.parent().clone(true, true);
                    realElement = $(clone.children().get(0));
                } else {

                    clone = realElement = element.clone();
                }

                // Reset value and increase id
                realElement
                    .val('')
                    .attr('id', element.attr('id').replace(/(\d+)/, function() {
                        return parseInt(arguments[1]) + 1;
                    }));

                // Needed for re-initializing CKEditor
                if (clone.data('widget') == '(ckeditor):invoked') {
                    clone.data('widget', 'ckeditor');
                }

                return clone;
            },

            /**
             * Adds new element into the scope
             *
             * @param scope Element object.
             * @return bool
             */
            addElementTo: function (scope) {

                if (this._canAdd(scope)) {

                    var name = scope.data('dynamic'),
                        element = $('[name="'+ name +'"]', scope).last(),
                        clone = this.cloneElement(element);

                    clone.insertBefore(scope.find('.dynamic-controls'));

                    // Initialize CKEditor
                    if (clone.data('widget') == 'ckeditor') {
                        root.widget.ckeditor.init(clone);
                    }

                    this.init(scope);
                }
                return false;
            },

            /**
             * Removes last element from the scope
             *
             * @param scope Element object.
             * @return bool
             */
            removeElementFrom: function (scope) {

                if (this._canRemove(scope)) {

                    var name = scope.data('dynamic'),
                        element = $('[name="'+ name +'"]', scope).last();

                    // Remove the last element
                    if (element.parent().hasClass('form_element_remote_file')) {
                        element.parent().remove();
                    } else {

                        if (element.data('widget') == '(ckeditor):invoked') {
                            root.widget.ckeditor.destroy(element);
                        }

                        element.remove();
                    }

                    this.init(scope);
                }
                return false;
            },

            /**
             * Get current count of elements within scope
             *
             * @param scope Element object.
             *
             * @private
             * @returns bool
             */
            _getCurrentCount: function(scope) {
                return $('[name="'+ scope.data('dynamic') +'"]', scope).length;
            },

            /**
             * Check if a new element can be added
             *
             * @param scope Element object.
             *
             * @private
             * @returns bool
             */
            _canAdd: function(scope) {
                return (this._getCurrentCount(scope) < (scope.data('dynamic-max') || 2));
            },

            /**
             * Check if the last element can be removed from scope
             *
             * @param scope Element object.
             *
             * @private
             * @returns bool
             */
            _canRemove: function(scope) {
                return (this._getCurrentCount(scope) > (scope.data('dynamic-min') || 1));
            }
        }
    );
}(window, jQuery, PhalconEye));
