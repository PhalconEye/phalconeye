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

namespace Core\Form\Admin\Language;

class Create extends \Engine\Form
{

    public function __construct($model = null)
    {
        if ($model === null){
            $model = new \Core\Model\Language();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Language Creation")
            ->setOption('description', "Create new language.")
            ;

        $this->addElement('text', 'name', array(
            'label' => 'Name'
        ));

        $this->addElement('text', 'locale', array(
            'label' => 'Locale'
        ));

        $this->addElement('file', 'icon', array(
            'label' => 'Icon'
        ));


        $this->addButton('Create', true);
        $this->addButtonLink('Cancel',  array('for' => 'admin-languages'));

    }
}