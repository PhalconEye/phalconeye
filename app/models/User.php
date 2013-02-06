<?php

class User extends \Phalcon\Mvc\Model
{

    /**
     * @var int
     *
     */
    protected $id;

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
