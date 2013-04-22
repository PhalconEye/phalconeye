<?php
/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine\Form\Element;

class Radio extends \Phalcon\Forms\Element\Select implements \Engine\Form\ElementInterface
{
    protected $_description;

    /**
     * If element is need to be rendered in default layout
     *
     * @return bool
     */
    public function useDefaultLayout(){
        return true;
    }

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
     * Sets the element description
     *
     * @param string $description
     * @return Form_ElementInterface
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }


    /**
     * Returns the element's description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    public function render($attributes = null)
    {
        $content = '';
        $options = $this->getOptions();
        $attributes = $this->getAttributes();;
        $value = (isset($attributes['value']) ? $attributes['value'] : null);

        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $content .= sprintf('<div class="form_element_radio"><input type="radio" value="%s" %s name="url_type" id="url_type"><label>%s</label></div>',
                    $key,
                    ($key == $value ? 'checked="checked"' : ''),
                    $option
                );
            }
        } else {
            if (!isset($attributes['using']) || !is_array($attributes['using']) || count($attributes['using']) != 2)
                throw new \Phalcon\Forms\Exception("The 'using' parameter is required to be an array with 2 values.");
            $keyAttribute = array_shift($attributes['using']);
            $valueAttribute = array_shift($attributes['using']);
            foreach ($options as $option) {
                /** @var \Phalcon\Mvc\Model $option */
                $optionKey = $option->readAttribute($keyAttribute);
                $optionValue = $option->readAttribute($valueAttribute);
                $content .= sprintf('<div class="form_element_radio"><input type="radio" value="%s" %s name="url_type" id="url_type"><label>%s</label></div>',
                    $optionKey,
                    ($optionKey == $value ? 'checked="checked"' : ''),
                    $optionValue
                );
            }
        }

        return $content;
    }
}