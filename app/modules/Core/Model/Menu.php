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

class Menu extends \Phalcon\Mvc\Model
{

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    protected $name;


    public function initialize()
    {
        $this->hasMany("id", '\Core\Model\MenuItem', "menu_id");
    }

    /**
     * Return the related "MenuItem"
     *
     * @return \Core\Model\MenuItem[]
     */
    public function getMenuItem($arguments = array()){
        return $this->getRelated('\Core\Model\MenuItem', $arguments);
    }


    /**
     * Method to set the value of field id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getSource()
    {
        return "menus";
    }

    public function beforeDelete()
    {
        $this->getMenuItem()->delete();
    }
}
