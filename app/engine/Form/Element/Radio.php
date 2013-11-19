<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Engine\Form\Element;

use Engine\Exception;
use Engine\Form\ElementInterface;
use Phalcon\Forms\Element\Select;

/**
 * Form element - Radiobox.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Radio extends Select implements ElementInterface
{
    const
        /**
         * Defines html patter for this element.
         */
        HTML_PATTERN = '<div class="form_element_radio"><input type="radio" value="%s" %s name="url_type" id="url_type"><label>%s</label></div>';
    /**
     * Radio description.
     *
     * @var string
     */
    protected $_description;

    /**
     * Create element.
     *
     * @param string $name       Element name.
     * @param null   $options    Element options.
     * @param null   $attributes Element attributes.
     */
    public function __construct($name, $options = null, $attributes = null)
    {
        $optionsData = (!empty($options['options']) ? $options['options'] : null);
        unset($options['options']);
        if (!is_array($attributes))
            $attributes = array();
        $options = array_merge($options, $attributes);
        parent::__construct($name, $optionsData, $options);
    }

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function useDefaultLayout()
    {
        return true;
    }

    /**
     * Returns the element's description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the element description.
     *
     * @param string $description Description text.
     *
     * @return ElementInterface
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Render current element to html.
     *
     * @param null|array $attributes Html attributes.
     *
     * @return string
     * @throws Exception
     */
    public function render($attributes = null)
    {
        $content = '';
        $options = $this->getOptions();
        $attributes = array_merge($this->getAttributes(), $attributes);
        $value = (isset($attributes['value']) ? $attributes['value'] : null);

        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $content .= sprintf(self::HTML_PATTERN, $key, ($key == $value ? 'checked="checked"' : ''), $option);
            }

            return $content;
        }

        return $this->_renderUsing($attributes, $options, $value);
    }

    /**
     * Render element with 'using' attribute.
     *
     * @param array $attributes Html attributes.
     * @param array $options    Element options.
     * @param mixed $value      Element value.
     *
     * @return string
     * @throws \Engine\Exception
     */
    protected function _renderUsing($attributes, $options, $value)
    {
        if (!isset($attributes['using']) || !is_array($attributes['using']) || count($attributes['using']) != 2) {
            throw new Exception("The 'using' parameter is required to be an array with 2 values.");
        }

        $content = '';
        $keyAttribute = array_shift($attributes['using']);
        $valueAttribute = array_shift($attributes['using']);
        foreach ($options as $option) {
            /** @var \Phalcon\Mvc\Model $option */
            $optionKey = $option->readAttribute($keyAttribute);
            $optionValue = $option->readAttribute($valueAttribute);
            $content .= sprintf(self::HTML_PATTERN, $optionKey, ($optionKey == $value ? 'checked="checked"' : ''), $optionValue);
        }

        return $content;
    }
}