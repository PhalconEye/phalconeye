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

class Select extends \Phalcon\Forms\Element\Select implements \Engine\Form\ElementInterface
{
    protected $_description;

    public function __construct($name, $options=null, $attributes=null){
        $optionsData = (!empty($options['options']) ? $options['options'] : null);
        unset($options['options']);
        if (!is_array($attributes))
            $attributes = array();
        $options = array_merge($options, $attributes);
        parent::__construct($name, $optionsData, $options);
    }

    /**
     * If element is need to be rendered in default layout
     *
     * @return bool
     */
    public function useDefaultLayout(){
        return true;
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
}