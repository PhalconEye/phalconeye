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

namespace Core\Helper;

use Core\Model\Settings;
use Engine\HelperInterface;
use Phalcon\DiInterface;
use Phalcon\Tag;

/**
 * System settings helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Setting extends Tag implements HelperInterface
{
    /**
     * Get setting from database.
     *
     * @param DiInterface $di   Dependency injection.
     * @param array       $args Helper arguments.
     *
     * @return mixed
     * @todo: Refactor helpers.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    static public function _(DiInterface $di, array $args)
    {
        return Settings::getSetting($args[0], (!isset($args[1]) ? null : $args[1]));
    }
}