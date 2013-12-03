<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Admin\Package;

use Engine\Form;
use Engine\Package\Manager;

/**
 * Export package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Export extends Form
{
    /**
     * Exclude data.
     *
     * @var array|null
     */
    protected $_exclude;

    /**
     * Form constructor.
     *
     * @param null|array $exclude Exclude data.
     */
    public function __construct($exclude = null)
    {
        $this->_exclude = $exclude;
        parent::__construct();
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        $this->setOption('description', 'Select package dependency (not necessarily).');
        if ($this->_exclude['type'] != Manager::PACKAGE_TYPE_LIBRARY) {
            $query = $this->getDI()->get('modelsManager')->createBuilder()
                ->from(array('t' => '\Core\Model\Package'))
                ->where("t.type = :type:", array('type' => Manager::PACKAGE_TYPE_MODULE))
                ->andWhere("t.enabled = :enabled:", array('enabled' => true));

            if ($this->_exclude && $this->_exclude['type'] == Manager::PACKAGE_TYPE_MODULE) {
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
            ->where("t.type = :type:", array('type' => Manager::PACKAGE_TYPE_LIBRARY))
            ->andWhere("t.enabled = :enabled:", array('enabled' => true));

        if ($this->_exclude && $this->_exclude['type'] == Manager::PACKAGE_TYPE_LIBRARY) {
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