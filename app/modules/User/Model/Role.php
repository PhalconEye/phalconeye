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
 * @Source("roles")
 * @HasMany("id", '\User\Model\User', "role_id", {
 *  "alias": "User"
 * })
 * @HasMany("id", '\Core\Model\Access', "role_id", {
 *  "alias": "Access"
 * })
 */
class Role extends \Engine\Model
{

    /**
     * @Primary
     * @Identity
     * @Column(type="integer", nullable=false, column="id")
     */
    public $id;

    /**
     * @Column(type="string", nullable=false, column="name")
     */
    public $name;

    /**
     * @Column(type="string", nullable=false, column="description")
     */
    public $description;

    /**
     * @Column(type="boolean", nullable=false, column="is_default")
     */
    public $is_default = false;

    /**
     * @Column(type="string", nullable=false, column="type")
     */
    public $type = 'user';

    /**
     * @Column(type="boolean", nullable=false, column="undeletable")
     */
    public $undeletable = false;


    /**
     * Return the related "User"
     *
     * @return \User\Model\User[]
     */
    public function getUser($arguments = array()){
        return $this->getRelated('User', $arguments);
    }

    /**
     * Return the related "Access"
     *
     * @return \Core\Model\Access[]
     */
    public function getAccess($arguments = array()){
        return $this->getRelated('Access', $arguments);
    }

    protected function beforeValidation(){
        if (empty($this->is_default)){
            $this->is_default = 0;
        }
    }

    protected function beforeDelete(){
        // cleanup acl
        $this->_modelsManager->executeQuery(
            "DELETE FROM Core\\Model\\Access WHERE role_id = ".$this->id.""
        );
    }

    /**
     * Get guest role by type
     *
     * @return Role
     */
    public static function getRoleByType($type){
        $role = Role::findFirst(array(
            "type = '{$type}'",
            'cache' => array(
                'key' => 'role_type_'.$type.'.cache'
            )
        ));
        if (!$role){
            $role = new Role();
            $role->name = ucfirst($type);
            $role->description = ucfirst($type). ' role.';
            $role->type = $type;
            $role->undeletable = 1;
            $role->save();
        }

        return $role;
    }

    /**
     * Get default guest role
     *
     * @return Role
     */
    public static function getDefaultRole(){
        $role = Role::findFirst(array(
            "is_default = 1",
            'cache' => array(
                'key' => 'role_default.cache'
            )
        ));
        if (!$role){
            $role = new Role();
            $role->name = "User";
            $role->description = 'Default user role.';
            $role->type = 'user';
            $role->undeletable = 1;
            $role->save();
        }

        return $role;
    }
}
