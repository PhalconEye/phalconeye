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
  +------------------------------------------------------------------------+
*/

namespace Core\Model;

use Engine\Db\AbstractModel;

/**
 * Access.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("access")
 * @BelongsTo("role_id", "User\Model\RoleModel", "id")
 */
class AccessModel extends AbstractModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type="string", nullable=false, column="object", size="55")
     */
    public $object;

    /**
     * @Primary
     * @Column(type="string", nullable=false, column="action", size="255")
     */
    public $action;

    /**
     * @Primary
     * @Column(type="integer", nullable=false, column="role_id", size="11")
     */
    public $role_id;

    /**
     * @Column(type="string", nullable=true, column="value", size="25")
     */
    public $value;
}
