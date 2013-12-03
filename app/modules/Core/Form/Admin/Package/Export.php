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

class Export extends \Engine\Form
{

    protected $_exclude;

    public function __construct($exclude = null)
    {
        $this->_exclude = $exclude;
        parent::__construct();
    }

    public function init()
    {
        $this->setOption('description', 'Select package dependency (not necessarily).');
        if ($this->_exclude['type'] != \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY) {
            $query = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
                ->from(array('t' => '\Core\Model\Package'))
                ->where("t.type = :type:", array('type' => \Engine\Package\Manager::PACKAGE_TYPE_MODULE))
                ->andWhere("t.enabled = :enabled:", array('enabled' => true));

            if ($this->_exclude && $this->_exclude['type'] == \Engine\Package\Manager::PACKAGE_TYPE_MODULE) {
                $query->andWhere("t.name != :name:", array('name' => $this->_exclude['name']));
            }

            $this->addElement('select', 'modules', array(
                'label' => 'Modules',
                'options' => $query->getQuery()->execute(),
                'using' => array('name', 'title'),
                'multiple' => 'multiple'
            ));
        }

        $query = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
            ->from(array('t' => '\Core\Model\Package'))
            ->where("t.type = :type:", array('type' => \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY))
            ->andWhere("t.enabled = :enabled:", array('enabled' => true));

        if ($this->_exclude && $this->_exclude['type'] == \Engine\Package\Manager::PACKAGE_TYPE_LIBRARY) {
            $query->andWhere("t.name != :name:", array('name' => $this->_exclude['name']));
        }

        $this->addElement('select', 'libraries', array(
            'label' => 'Libraries',
            'options' => $query->getQuery()->execute(),
            'using' => array('name', 'title'),
            'multiple' => 'multiple'
        ));

    }
}