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

namespace Core\Form\Admin\Page;

class Create extends \Engine\Form
{

    public function __construct($model = null)
    {

        if ($model === null){
            $model = new \Core\Model\Page();
        }

        $model->prepareRoles();

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Page Creation")
            ->setOption('description', "Create new page.");

        $this->addElement('text', 'title', array(
            'label' => 'Title',
        ));

        $this->addElement('text', 'url', array(
            'label' => 'Url',
            'description' => 'Page will be available under http://'.$_SERVER['HTTP_HOST'].'/page/[URL NAME]'
        ));

        $this->addElement('textArea', 'description', array(
            'label' => 'Description'
        ));

        $this->addElement('textArea', 'keywords', array(
            'label' => 'Keywords'
        ));

        $this->addElement('text', 'controller', array(
            'label' => 'Controller',
            'description' => 'Controller and action name that will handle this page. Example: NameController->someAction'
        ));

        $this->addElement('select', 'roles', array(
            'label' => 'Roles',
            'description' => 'If no value is selected, will be allowed to all (also as all selected).',
            'options' => \User\Model\Role::find(),
            'using' => array('id', 'name'),
            'multiple' => 'multiple'
        ));



        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-pages'));

    }
}