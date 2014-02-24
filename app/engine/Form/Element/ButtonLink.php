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
 * Form element - ButtonLink.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ButtonLink extends AbstractElement implements ElementInterface
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
        return ['htmlTemplate', 'label'];
    }

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return array_merge(parent::getDefaultAttributes(), ['class' => 'btn form_link_button']);
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption('htmlTemplate', '<a' . $this->_renderAttributes() . '>%s</a>');
    }

    /**
     * Render element.
     *
     * @return string
     */
    public function render()
    {
        $href = $this->getValue();
        if ($href) {
            $href = $this->getDI()->get('url')->get($href);
        } else {
            $href = 'javascript:;';
        }
        $this->setAttribute('href', $href);

        return sprintf(
            $this->getHtmlTemplate(),
            $this->_($this->getOption('label'))
        );
    }
}