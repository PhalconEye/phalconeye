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

use Engine\HelperInterface;
use Phalcon\DiInterface;
use Phalcon\Tag;

/**
 * Javascript translator helper.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class JsTrans extends Tag implements HelperInterface
{
    /**
     * Output javascript translation scope.
     *
     * @param DiInterface $di   Dependency injection.
     * @param array       $args Helper arguments.
     *
     * @return mixed
     */
    static public function _(DiInterface $di, array $args)
    {
        $content = 'var translatorData = translatorData || [];' . PHP_EOL;
        foreach ($args as $text) {
            $content .= 'translatorData["' . $text . '"] = "' . $di->get('trans')->query($text) . '";' . PHP_EOL;
        }

        return $content;
    }
}