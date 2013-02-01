<?php


class Widgets extends \Phalcon\Mvc\Model 
{

    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var integer
     *
     */
    protected $module_id;

    /**
     * @var string
     *
     */
    protected $name;

    /**
     * @var string
     *
     */
    protected $title;

    /**
     * @var string
     *
     */
    protected $description;

    /**
     * @var integer
     *
     */
    protected $is_paginated;

    /**
     * @var string
     *
     */
    protected $admin_form;


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
     * Method to set the value of field module_id
     *
     * @param integer $module_id
     */
    public function setModuleId($module_id)
    {
        $this->module_id = $module_id;
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
     * Method to set the value of field title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Method to set the value of field is_paginated
     *
     * @param integer $is_paginated
     */
    public function setIsPaginated($is_paginated)
    {
        $this->is_paginated = $is_paginated;
    }

    /**
     * Method to set the value of field admin_form
     *
     * @param string $admin_form
     */
    public function setAdminForm($admin_form)
    {
        $this->admin_form = $admin_form;
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
     * Returns the value of field module_id
     *
     * @return integer
     */
    public function getModuleId()
    {
        return $this->module_id;
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

    /**
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the value of field is_paginated
     *
     * @return integer
     */
    public function getIsPaginated()
    {
        return $this->is_paginated;
    }

    /**
     * Returns the value of field admin_form
     *
     * @return string
     */
    public function getAdminForm()
    {
        return $this->admin_form;
    }

    public function getSource()
    {
        return "widgets";
    }

}
