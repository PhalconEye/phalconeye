<?php


class Role extends \Phalcon\Mvc\Model
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

    /**
     * @var string
     * @form_type textArea
     *
     */
    protected $description;

    /**
     * @var int
     * @form_type checkField
     *
     */
    protected $is_default = 0;


    /**
     * @var string
     *
     */
    protected $type = 'user';


    /**
     * @var integer
     *
     */
    protected $undeletable = 0;


    public function initialize()
    {
        $this->hasMany("id", "User", "role_id");
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
     * Method to set the value of field description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Method to set the value of field is_default
     *
     * @param integer $is_default
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = $is_default;
    }

    /**
     * Method to set the value of field type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Method to set the value of field undeletable
     *
     * @param integer $undeletable
     */
    public function setUndeletable($undeletable)
    {
        $this->undeletable = $undeletable;
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
    {return $this->name;
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
     * Returns the value of field is_default
     *
     * @return integer
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * Returns the value of field undeletable
     *
     * @return integer
     */
    public function getUndeletable()
    {
        return $this->undeletable;
    }


    public function getSource()
    {
        return "roles";
    }

    public function beforeValidation(){
        if (empty($this->is_default)){
            $this->is_default = 0;
        }
    }

    /**
     * Get default guest role
     *
     * @return Role
     */
    public static function getRoleByType($type){
        $role = Role::findFirst("type = '{$type}'");
        if (!$role){
            $role = new Role();
            $role->setName(ucfirst($type));
            $role->setDescription(ucfirst($type). ' role.');
            $role->setType($type);
            $role->setUndeletable(1);
            $role->save();
        }

        return $role;
    }
}
