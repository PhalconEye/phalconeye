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

namespace Core\Model;

class Access extends \Phalcon\Mvc\Model 
{

    /**
     * @var string
     *
     */
    protected $object;

    /**
     * @var string
     *
     */
    protected $action;

    /**
     * @var integer
     *
     */
    protected $role_id;

    /**
     * @var string
     *
     */
    protected $value;



    public function initialize()
    {
           $this->belongsTo("role_id", "Role", "id");
    }

    /**
     * Method to set the value of field object
     *
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * Method to set the value of field action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Method to set the value of field role_id
     *
     * @param integer $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * Method to set the value of field value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


    /**
     * Returns the value of field object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Returns the value of field action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the value of field role_id
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getSource()
    {
        return "access";
    }
}
