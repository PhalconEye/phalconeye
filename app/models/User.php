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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class User extends \Phalcon\Mvc\Model
{

    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var int
     *
     */
    protected $role_id;

    /**
     * @var string
     *
     */
    protected $username;

    /**
     * @var string
     * @form_type passwordField
     *
     */
    protected $password;

    /**
     * @var string
     *
     */
    protected $email;

    /**
     * @var string
     *
     */
    protected $creation_date;

    /**
     * Current viewer
     *
     * @var User null
     */
    private static $_viewer = null;


    public function initialize()
    {
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable(
            array(
                'beforeCreate' => array(
                    'field' => 'creation_date',
                    'format' => 'Y-m-d H:i:s'
                )
            )
        ));

        $this->belongsTo("role_id", "Role", "id");
    }


    /**
     * Method to set the value of field id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Method to set the value of field role_id
     *
     * @param int $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * Method to set the value of field username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Method to set the value of field creation_date
     *
     * @param string $creation_date
     */
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
    }


    /**
     * Returns the value of field id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field role_id
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field creation_date
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    public function isAdmin(){
        return $this->getRole()->getType() == Api_Acl::ROLE_TYPE_ADMIN;
    }

    /**
     * Get current user
     * If user logged in this function will return user object with data
     * If user isn't logged in this function will return empty user object with ID = 0
     *
     * @return null|Phalcon\Mvc\Model\ResultsetInterface|User
     */
    public static function getViewer(){
        if (null === self::$_viewer) {
            $identity = Phalcon\DI::getDefault()->get('auth')->getIdentity();
            self::$_viewer = self::findFirst($identity);
            if (!self::$_viewer){
                self::$_viewer = new User();
                self::$_viewer->setId(0);
                self::$_viewer->setRoleId(Role::getRoleByType(Api_Acl::ROLE_TYPE_GUEST)->getId());
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

    public function getSource()
    {
        return "users";
    }
}
