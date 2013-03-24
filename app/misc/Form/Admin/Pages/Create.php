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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Form_Admin_Pages_Create extends Form
{

    public function __construct($model = null)
    {
        $this
            ->addIgnored('view_count')
            ->addIgnored('layout')
            ->addIgnored('type')
        ;

        if ($model === null){
            $model = new Page();
        }

        $model->prepareRoles();

        parent::__construct($model);

        $this->setElementParam('url', 'description', 'Page will be available under http://'.$_SERVER['HTTP_HOST'].'/page/[URL NAME]');
        $this->setElementParam('controller', 'description', 'Controller and action name that will handle this page. Example: NameController->someAction');

        $this->setElementAttrib('roles', 'multiple', 'multiple');
        $this->setElementParam('roles', 'options', Role::find());
        $this->setElementParam('roles', 'using', array('id', 'name'));
        $this->setElementParam('roles', 'description', 'If no value is selected, will be allowed to all (also as all selected).');

    }

    public function init()
    {
        $this
            ->setOption('title', "Page Creation")
            ->setOption('description', "Create new page.");


        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-pages'));

    }
}