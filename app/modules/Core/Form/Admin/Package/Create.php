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

class Create extends \Engine\Form
{

    public function __construct($model = null)
    {

        if ($model === null) {
            $model = new \Core\Model\Package();
        }

        parent::__construct($model);
    }

    public function init()
    {
        $this
            ->setOption('title', "Package Creation")
            ->setOption('description', "Create new package.");

        $this->addElement('text', 'name', array(
            'label' => 'Name',
            'description' => 'Name must be in lowecase and contains only letters.',
            'validators' => array(
                new \Engine\Form\Validator\Regex(array(
                    'pattern' => '/[a-z]+/',
                    'message' => 'Name must be in lowecase and contains only letters.'
                ))
            )
        ));

        $this->addElement('select', 'type', array(
            'label' => 'Package type',
            'options' => \Engine\Package\Manager::$allowedTypes
        ));

        $this->addElement('text', 'title', array(
            'label' => 'Title'
        ));

        $this->addElement('textArea', 'description', array(
            'label' => 'Description'
        ));

        $this->addElement('text', 'version', array(
            'label' => 'Version',
            'description' => 'Type package version. Ex.: 0.5.7',
            'validators' => array(
                new \Engine\Form\Validator\Regex(array(
                    'pattern' => '/\d+(\.\d+)+/',
                    'message' => 'Version must be in correct format: 1.0.0 or 1.0.0.0'
                ))
            )
        ));

        $this->addElement('text', 'author', array(
            'label' => 'Author',
            'description' => 'Who create this package? Identify youself!'
        ));

        $this->addElement('text', 'website', array(
            'label' => 'Website',
            'description' => 'Where user will look for new version?'
        ));

        $this->addElement('textArea', 'header', array(
            'label' => 'Header comments',
            'description' => 'This text will be placed in each file of package. Use comment block /**  **/.'
        ));

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', array('for' => 'admin-packages'));

    }

    public function isValid($data = null, $entity = null, $skipEntityCreation = false)
    {
        if (!parent::isValid($data, null, true))
            return false;

        // check package existence
        /** @var \Phalcon\Mvc\Model\Query\Builder $query  */
        $query = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
            ->from(array('t' => '\Core\Model\Package'))
            ->where('t.type = :type: AND t.name = :name:', array('type' => $data['type'], 'name' => $data['name']));

        /** @var \Phalcon\Mvc\Model\Resultset\Simple $package  */
        $package = $query->getQuery()->execute();
        if ($package->count() == 1){
            $this->addError('Package with that name already exist!');
            return false;
        }

        $this->getEntity()->save();
        return true;
    }
}