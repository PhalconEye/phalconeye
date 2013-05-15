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

class Html extends \Engine\Form\Element implements \Engine\Form\ElementInterface
{
    protected $_html = '';

    /**
     * If element is need to be rendered in default layout
     *
     * @return bool
     */
    public function useDefaultLayout(){
        return false;
    }

    public function __construct($name, $attributes=null){
        if (isset($attributes['html'])){
            $this->_html = $attributes['html'];
            unset($attributes['html']);
        }

        parent::__construct($name, $attributes);
    }

    public function render(){
        return $this->_html;
    }
}