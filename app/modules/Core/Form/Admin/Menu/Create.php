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

namespace Core\Form\Admin\Menu;

class Create extends \Engine\Form
{

    public function __construct($model = null)
    {

        if ($model === null){
            $model = new \Core\Model\Menu();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Menu Creation")
            ->setOption('description', "Create new menu.");

        $this->addElement('text', 'name', array(
            'label' => 'Name'
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-menus'));

    }
}