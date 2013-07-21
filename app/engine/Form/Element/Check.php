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

class Check extends \Phalcon\Forms\Element\Check implements \Engine\Form\ElementInterface
{
    protected $_description;

    public function __construct($name, $attributes=null){
        if (isset($attributes['value']) && $attributes['value'] == true){
            $attributes['checked'] = 'checked';
        }

        if (isset($attributes['options'])){
            $attributes['value'] = $attributes['options'];
            unset($attributes['options']);
        }

        parent::__construct($name, $attributes);
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
     * @return \Engine\Form\ElementInterface
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

    public function setDefault($value)
    {
        if ($value == true) {
            $this->setAttribute('checked', 'checked');
        } else {
            $attributes = $this->getAttributes();
            unset($attributes['checked']);
            $this->setAttributes($attributes);
        }

        parent::setDefault($value);
    }

    public function prepareAttributes($attributes = NULL, $useChecked = NULL){
        if (!is_array($attributes))
            $attributes = array();

        $attributes = array_merge(array($this->_name), $attributes);
        return array_merge($attributes, $this->getAttributes());
    }


}