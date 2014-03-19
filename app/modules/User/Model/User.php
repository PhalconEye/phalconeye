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

namespace User\Model;

use Core\Api\Acl;
use Engine\Db\AbstractModel;
use Engine\Db\Model\Behavior\Timestampable;
use Phalcon\DI;
use Phalcon\Mvc\Model\Validator\Email;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * User.
 *
 * @category  PhalconEye
 * @package   User\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("users")
 * @BelongsTo("role_id", '\User\Model\Role', "id", {
 *  "alias": "Role"
 * })
 */
class User extends AbstractModel
{
    const
        /**
         * Cache prefix.
         */
        CACHE_PREFIX = 'role_id_';

    // use trait Timestampable for creation_date and modified_date fields.
    use Timestampable;

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id", size="11")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=false, column="role_id", size="11")
     */
    public $role_id;

    /**
     * @Index("ix_username")
     * @Column(type="string", nullable=false, column="username", size="255")
     */
    public $username;

    /**
     * @Column(type="string", nullable=false, column="password", size="255")
     */
    public $password;

    /**
     * @Index("ix_email")
     * @Column(type="string", nullable=false, column="email", size="150")
     */
    public $email;

    /**
     * Current viewer.
     *
     * @var User null
     */
    private static $_viewer = null;

    /**
     * Set user password.
     *
     * @param string $password User password.
     *
     * @return void
     */
    public function setPassword($password)
    {
        if ($this->getId() === null || !empty($password) && $this->password != $password) {
            $this->password = $this->getDI()->get('security')->hash($password);
        }
    }

    /**
     * Return the related "Role" entity.
     *
     * @param array $arguments Arguments data.
     *
     * @return Role
     */
    public function getRole($arguments = [])
    {
        $arguments = array_merge(
            $arguments,
            [
                'cache' => [
                    'key' => self::CACHE_PREFIX . $this->role_id
                ]
            ]
        );
        $role = $this->getRelated('Role', $arguments);
        if (!$role) {
            $role = new Role();
            $role->id = 0;
            $role->name = '';
        }

        return $role;
    }

    /**
     * Will check if user have Admin role.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getRole()->type == Acl::DEFAULT_ROLE_ADMIN;
    }

    /**
     * Get current user
     * If user logged in this function will return user object with data
     * If user isn't logged in this function will return empty user object with ID = 0
     *
     * @return User
     */
    public static function getViewer()
    {
        if (null === self::$_viewer) {
            $identity = DI::getDefault()->get('core')->auth()->getIdentity();
            if ($identity) {
                self::$_viewer = self::findFirst($identity);
            }
            if (!self::$_viewer) {
                self::$_viewer = new User();
                self::$_viewer->id = 0;
                self::$_viewer->role_id = Role::getRoleByType(Acl::DEFAULT_ROLE_GUEST)->id;
            }
        }

        return self::$_viewer;
    }

    /**
     * Validations and business logic.
     *
     * @return bool
     */
    public function validation()
    {
        if ($this->_errorMessages === null) {
            $this->_errorMessages = [];
        }

        $this->validate(new Uniqueness(["field" => "username"]));
        $this->validate(new Uniqueness(["field" => "email"]));
        $this->validate(new Email(["field" => "email", "required" => true]));
        $this->validate(new StringLength(["field" => "password", "min" => 6]));

        return $this->validationHasFailed() !== true;
    }
}