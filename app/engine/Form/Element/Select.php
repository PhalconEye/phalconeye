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

use Engine\Form\Element\Traits\Description;
use Engine\Form\ElementInterface;
use Phalcon\Forms\Element\Select as PhalconSelect;

/**
 * Form element - Select.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Select extends PhalconSelect implements ElementInterface
{
    use Description;

    /**
     * Create select element.
     *
     * @param string $name       Element name.
     * @param null   $options    Element options.
     * @param null   $attributes Element attributes.
     */
    public function __construct($name, $options = null, $attributes = null)
    {
        $optionsData = (!empty($options['options']) ? $options['options'] : null);
        unset($options['options']);
        if (!is_array($attributes)) {
            $attributes = [];
        }
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
}