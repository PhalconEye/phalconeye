<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace User\Model;

/**
 * @Source("users")
 * @BelongsTo("role_id", '\User\Model\Role', "id", {
 *  "alias": "Role"
 * })
 */
class User extends \Engine\Model
{

    // use trait Timestampable for creation_date and modified_date fields
    use \Engine\Model\Behavior\Timestampable;

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id")
     */
    public $id;

    /**
     * @Column(type="integer", nullable=true, column="role_id")
     */
    public $role_id;

    /**
     * @Column(type="string", nullable=false, column="username")
     */
    public $username;

    /**
     * @Column(type="string", nullable=false, column="password")
     */
    public $password;

    /**
     * @Column(type="string", nullable=false, column="email")
     */
    public $email;

    /**
     * Current viewer
     *
     * @var User null
     */
    private static $_viewer = null;

    public function setPassword($password){
        if (!empty($password) && $this->password != $password)
            $this->password = $this->getDI()->get('security')->hash($password);
    }

    /**
     * Return the related "Role"
     *
     * @return \User\Model\Role
     */
    public function getRole($arguments = array()){
        $role = $this->getRelated('Role', $arguments);
        if (!$role){
            $role = new Role();
            $role->id = 0;
            $role->name = '';
        }

        return $role;
    }

    /**
     * Will check if user have Admin role
     *
     * @return bool
     */
    public function isAdmin(){
        return $this->getRole()->type == \Core\Api\Acl::ROLE_TYPE_ADMIN;
    }

    /**
     * Get current user
     * If user logged in this function will return user object with data
     * If user isn't logged in this function will return empty user object with ID = 0
     *
     * @return null|\Phalcon\Mvc\Model\ResultsetInterface|User
     */
    public static function getViewer(){
        if (null === self::$_viewer) {
            $identity = \Phalcon\DI::getDefault()->get('core')->auth()->getIdentity();
            self::$_viewer = self::findFirst($identity);
            if (!self::$_viewer){
                self::$_viewer = new User();
                self::$_viewer->id = 0;
                self::$_viewer->role_id = Role::getRoleByType(\Core\Api\Acl::ROLE_TYPE_GUEST)->id;
            }
        }

        return self::$_viewer;
    }

    /**
     * Validations and business logic 
     */
    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            "field" => "username"
        )));

        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(array(
            "field" => "email"
        )));

        $this->validate(new \Phalcon\Mvc\Model\Validator\Email(array(
            "field" => "email",
            "required" => true
        )));

        $this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
            "field" => "password",
            "min" => 6
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
