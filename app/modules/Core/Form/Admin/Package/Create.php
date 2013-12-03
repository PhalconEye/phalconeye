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

use Core\Model\Package;
use Engine\Db\AbstractModel;
use Engine\Form;
use Engine\Form\Validator\Regex;
use Engine\Package\Manager;

/**
 * Create package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Create extends Form
{
    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     */
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new Package();
        }

        parent::__construct($model);
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('title', "Package Creation")
            ->setOption('description', "Create new package.");

        $this->addElement('text', 'name', array(
            'label' => 'Name',
            'description' => 'Name must be in lowecase and contains only letters.',
            'validators' => array(
                new Regex(array(
                    'pattern' => '/[a-z]+/',
                    'message' => 'Name must be in lowecase and contains only letters.'
                ))
            )
        ));

        $this->addElement('select', 'type', array(
            'label' => 'Package type',
            'options' => Manager::$allowedTypes
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
                new Regex(array(
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

    /**
     * Validation method.
     *
     * @param null|array $data Model data.
     *
     * @return bool
     */
    public function isValid($data = null)
    {
        if (!parent::isValid($data, null, true)) {
            return false;
        }

        // Check package existence.
        /** @var \Phalcon\Mvc\Model\Query\Builder $query */
        $query = $this->getDI()->get('modelsManager')->createBuilder()
            ->from(array('t' => '\Core\Model\Package'))
            ->where('t.type = :type: AND t.name = :name:', array('type' => $data['type'], 'name' => $data['name']));

        /** @var \Phalcon\Mvc\Model\Resultset\Simple $package */
        $package = $query->getQuery()->execute();
        if ($package->count() == 1) {
            $this->addError('Package with that name already exist!');

            return false;
        }

        $this->getEntity()->save();

        return true;
    }
}