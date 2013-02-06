<?php

class Form_Admin_Users_Create extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('role_id')
        ;

        if ($model === null){
            $model = new User();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "User Creation")
            ->setOption('description', "Create new user.");


        $this->addButton('Create', true);
        $this->addButton('Cancel', false, array(
            'onclick' => "window.location.href='/admin/users'; return false;"
        ));

    }
}