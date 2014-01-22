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

namespace Engine\Db;

use Phalcon\DI;
use Phalcon\Mvc\Model as PhalconModel;

/**
 * Abstract Model.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method static findFirstById($id)
 *
 * @method \Engine\DependencyInjection|\Phalcon\DI getDI()
 */
abstract class AbstractModel extends PhalconModel
{
    /**
     * Get table name.
     *
     * @return string
     */
    public static function getTableName()
    {
        $reader = DI::getDefault()->get('annotations');
        $reflector = $reader->get(get_called_class());
        $annotations = $reflector->getClassAnnotations();

        return $annotations->get('Source')->getArgument(0);
    }
}