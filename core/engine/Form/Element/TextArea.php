<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Form\Element;

use Engine\Form\AbstractElement;
use Engine\Form\ElementInterface;

/**
 * Form element - Text area.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Element
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class TextArea extends AbstractElement implements ElementInterface
{
    /**
     * Get element html template.
     *
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->getOption('htmlTemplate', '<textarea' . $this->_renderAttributes() . '>%s</textarea>');
    }

}