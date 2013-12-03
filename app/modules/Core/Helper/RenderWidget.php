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
use Engine\Widget\Element;
use Phalcon\DiInterface;
use Phalcon\Tag;
use User\Model\User;

/**
 * Widget renderer.
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class RenderWidget extends Tag implements HelperInterface
{
    /**
     * Render widget.
     *
     * @param DiInterface $di   Dependency injection.
     * @param array       $args Helper arguments.
     *
     * @return mixed
     */
    static public function _(DiInterface $di, array $args)
    {
        if (!self::_isAllowed($args[1])) {
            return '';
        }
        $widget = new Element($args[0], $args[1], $di);

        return $widget->render();
    }

    /**
     * Check that this widget is allowed for current user.
     *
     * @param array $params User params.
     *
     * @return bool
     */
    protected static function _isAllowed($params)
    {
        $viewer = User::getViewer();
        if (empty($params['roles']) || !is_array($params['roles'])) {
            return true;
        }

        return in_array($viewer->role_id, $params['roles']);
    }
}