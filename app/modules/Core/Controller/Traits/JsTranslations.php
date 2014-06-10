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

namespace Core\Controller\Traits;

use Core\Helper\I18n;
use Engine\Helper;

/**
 * Js translations
 *
 * @category  PhalconEye
 * @package   Core\Controller\Traits
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait JsTranslations
{
    /**
     * Add default javascript translations.
     *
     * @return void
     */
    public function addDefaultJsTranslations()
    {
        I18n::getInstance($this->getDI())
            ->add('Do you really want to delete this item?')
            ->add('Close this window?');
    }
}