<?php

class Users extends \Phalcon\Mvc\Model 
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
     * Validations and business logic 
     */
    public function validation()
    {        
        $this->validate(new Email(array(
            "field" => "email",
            "required" => true
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
