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

use Engine\Form\AbstractElement;
use Engine\Form\Behaviour\TranslationBehaviour;
use Engine\Form\ElementInterface;

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
     * Sets the element option.
     *
     * @param string $value Element value.
     *
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->_value === null) {
            $this->_value = $value;
        } else {
            if ($this->_value == $value) {
                $this->setOption('checked', 'checked');
                $this->setOption('ignore', false);
            } else {
                $this->setOption('checked', null);
                $this->setOption('ignore', true);
            }
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
            <input type="checkbox" value="%s"' . $this->_renderAttributes() . '%s/>
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
            $this->getValue(),
            ($this->getOption('checked') ? ' checked="checked"' : '')
        );
    }
}