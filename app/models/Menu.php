<?php


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
        $this->hasMany("id", "MenuItem", "menu_id");
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
