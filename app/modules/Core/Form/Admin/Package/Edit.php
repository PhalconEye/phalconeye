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

namespace Core\Form\Admin\Package;

class Edit extends Create
{
    protected $_link;

    public function __construct($model = null, $link = 'admin-packages')
    {
        $this->_link = $link;

        if ($model === null) {
            $model = new \Core\Model\Package();
        }

        parent::__construct($model);
    }

    public function init()
    {
        parent::init();
        $this
            ->setOption('title', "Edit Package")
            ->setOption('description', "Edit this package.");

        $this->removeElement('name');
        $this->removeElement('type');
        $this->removeElement('header');

        $this->clearButtons();
        $this->addButton('Save', true);
        $this->addButtonLink('Cancel', array('for' => $this->_link));

    }
}