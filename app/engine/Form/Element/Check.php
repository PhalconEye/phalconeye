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

use Engine\Form\ElementInterface;
use Phalcon\Forms\Element\Check as PhalconCheck;

/**
 * Form element - Checkbox.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Check extends PhalconCheck implements ElementInterface
{
    use Description;

    /**
     * Create check element.
     *
     * @param string $name       Element name.
     * @param null   $attributes Element attributes.
     */
    public function __construct($name, $attributes = null)
    {
        if (isset($attributes['value']) && $attributes['value'] == true) {
            $attributes['checked'] = 'checked';
        }

        if (isset($attributes['options'])) {
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
    public function useDefaultLayout()
    {
        return true;
    }

    /**
     * Set default value.
     *
     * @param mixed $value Element value.
     *
     * @return ElementInterface
     */
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

        return $this;
    }

    /**
     * Prepare attributes.
     *
     * @param null $attributes Element attributes.
     *
     * @return array
     */
    public function prepareAttributes($attributes = null)
    {
        if (!is_array($attributes)) {
            $attributes = array();
        }

        $attributes = array_merge(array($this->_name), $attributes);

        return array_merge($attributes, $this->getAttributes());
    }
}