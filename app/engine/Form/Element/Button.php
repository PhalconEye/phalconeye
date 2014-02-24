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

use Engine\Form\AbstractElement;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Form\ElementInterface;

/**
 * Form element - Button.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Button extends AbstractElement implements ElementInterface
{
    use TranslationBehaviour;

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function useDefaultLayout()
    {
        return false;
    }

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function isIgnored()
    {
        return true;
    }

    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return ['htmlTemplate', 'label', 'isSubmit'];
    }

    /**
     * Get element default options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return ['isSubmit' => true];
    }

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return array_merge(
            parent::getDefaultAttributes(),
            ($this->getOption('isSubmit') ? ['type' => 'submit', 'class' => 'btn btn-primary'] : ['class' => 'btn'])
        );
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption('htmlTemplate', '<button' . $this->_renderAttributes() . '>%s</button>');
    }

    /**
     * Render element.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->getAttribute('value') && $this->getValue()) {
            $this->setAttribute('value', $this->getValue());
        }
        return sprintf(
            $this->getHtmlTemplate(),
            $this->_($this->getOption('label'))
        );
    }
}