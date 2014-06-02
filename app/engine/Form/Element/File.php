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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Form\Element;

use Engine\Form\ElementInterface;

/**
 * Form element - File selection.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class File extends AbstractInput implements ElementInterface
{
    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return array_merge(parent::getAllowedOptions(), ['isImage']);
    }

    /**
     * Get this input element type.
     *
     * @return string
     */
    public function getInputType()
    {
        return 'file';
    }

    /**
     * Returns the element's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();
        if ($this->getOption('isImage') && !empty($value)) {
            if ($this->isDynamic() && is_array($value)) {
                $values = [];
                foreach ($value as $one) {
                    $values[] = $this->getDI()->getUrl()->get($one);
                }
                return $values;
            } else {
                return $this->getDI()->getUrl()->get($value);
            }
        }
        return $value;
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        $value = $this->getValue();
        $html = $this->getOption('htmlTemplate', '<input' . $this->_renderAttributes() . '>');

        if (!empty($value) && $value != '/' && $this->getOption('isImage')) {
            $html = '<div class="form_element_file_image">
                         <img alt="'. $this->getOption('label', 'Preview image') .'" src="'. $value .'"/>
                     </div>'.
                    $html;
        }

        return $html;
    }
}
