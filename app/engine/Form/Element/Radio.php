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
use Engine\Form\Exception;

/**
 * Form element - Radiobox.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Radio extends AbstractElement implements ElementInterface
{
    use TranslationBehaviour;

    /**
     * Returns the element's option.
     *
     * @param string $name    Option name.
     * @param mixed  $default Default value.
     *
     * @throws \Engine\Form\Exception
     * @return mixed|null
     */
    public function getOption($name, $default = null)
    {
        if (!isset($this->_options[$name])) {
            return $default;
        }

        if ($name == 'elementOptions') {
            $elementOptions = $this->_options[$name];
            if (!is_array($elementOptions)) {
                $data = [];
                $using = $this->getOption('using');

                if (!$using || !is_array($using) || count($using) != 2) {
                    throw new Exception("The 'using' parameter is required to be an array with 2 values.");
                }

                $keyAttribute = array_shift($using);
                $valueAttribute = array_shift($using);

                foreach ($elementOptions as $option) {
                    /** @var \Phalcon\Mvc\Model $option */
                    $data[$option->readAttribute($keyAttribute)] = $option->readAttribute($valueAttribute);
                }

                $this->setOption('elementOptions', $data);
            }
        }

        return $this->_options[$name];
    }

    /**
     * Get allowed options for this element.
     *
     * @return array
     */
    public function getAllowedOptions()
    {
        return array_merge(parent::getAllowedOptions(), ['elementOptions', 'disabledOptions', 'using']);
    }

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return array_merge(parent::getDefaultAttributes(), ['class' => '']);
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
                    <input type="radio" value="%s"' . $this->_renderAttributes() . '%s%s/>
                    <label>%s</label>
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
        $elementOptions = $this->getOption('elementOptions');
        $disabledOptions = $this->getOption('disabledOptions', []);

        $content = '';
        foreach ($elementOptions as $key => $value) {
            $content .= sprintf(
                $this->getHtmlTemplate(),
                $key,
                ($key == $this->getValue() ? ' checked="checked"' : ''),
                (in_array($key, $disabledOptions) ? ' disabled="disabled"' : ''),
                $this->_($value)
            );
        }

        return $content;
    }
}