<?php

class Form_Admin_Users_RoleCreate extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('type')
            ->addIgnored('undeletable')
        ;

        if ($model === null){
            $model = new Role();
        }

        parent::__construct($model);

        $this->setElementParam('is_default', 'value', 1);
    }

    public function init()
    {
        $this
            ->setOption('title', "Role Creation")
            ->setOption('description', "Create new role.");


        $this->addButton('Create', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/admin/users/roles'; return false;"
        ));

    }
}