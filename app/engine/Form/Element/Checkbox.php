<?php
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

namespace Engine\Form\Element;

use Engine\Behaviour\TranslationBehaviour;
use Engine\Form\AbstractElement;
use Engine\Form\ElementInterface;

/**
 * Form element - Checkbox.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Checkbox extends AbstractElement implements ElementInterface
{
    use TranslationBehaviour;

    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return array_merge(parent::getAllowedOptions(), ['checked']);
    }

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return array_merge(parent::getDefaultAttributes(), ['type' => 'checkbox', 'class' => '']);
    }

    /**
     * Sets the element option.
     *
     * @param string $value  Element value.
     * @param bool   $escape Try to escape html in value.
     *
     * @return $this
     */
    public function setValue($value, $escape = false)
    {
        parent::setValue(($value == '' ? $this->getOption('defaultValue') : $value), $escape);
        if ($this->_value == $this->getAttribute('value')) {
            $this->setOption('checked', 'checked');
        } else {
            $this->setOption('checked', null);
        }

        return $this;
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption(
            'htmlTemplate',
            '
            <div class="form_element_radio">
            <input' . $this->_renderAttributes() . '%s/>
            </div>
            '
        );
    }

    /**
     * Render element.
     *
     * @return string
     */
    public function render()
    {
        return sprintf(
            $this->getHtmlTemplate(),
            ($this->getOption('checked') ? ' checked="checked"' : '')
        );
    }
}