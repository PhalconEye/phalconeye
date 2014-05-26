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

use Engine\Form\AbstractElement;
use Engine\Form\ElementInterface;

/**
 * Form element - input.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element\Abstract
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractInput extends AbstractElement implements ElementInterface
{
    /**
     * Get this input element type.
     *
     * @return string
     */
    abstract public function getInputType();

    /**
     * Get element default attribute.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return array_merge(parent::getDefaultAttributes(), ['type' => $this->getInputType()]);
    }

    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption('htmlTemplate', '<input' . $this->_renderAttributes() . ' value="%s">');
    }
}