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
use Engine\Application;
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

        $this->addElement(
            'text',
            'name',
            [
                'label' => 'Name',
                'description' => 'Name must be in lowecase and contains only letters.',
                'validators' => [
                    new Regex(
                        [
                            'pattern' => '/[a-z]+/',
                            'message' => 'Name must be in lowecase and contains only letters.'
                        ]
                    )
                ]
            ]
        );

        $this->addElement(
            'select',
            'type',
            [
                'label' => 'Package type',
                'options' => Manager::$allowedTypes
            ]
        );

        $this->addElement(
            'text',
            'title',
            [
                'label' => 'Title'
            ]
        );

        $this->addElement('textArea', 'description', ['label' => 'Description']);

        $this->addElement(
            'text',
            'version',
            [
                'label' => 'Version',
                'description' => 'Type package version. Ex.: 0.5.7',
                'validators' => [
                    new Regex(
                        [
                            'pattern' => '/\d+(\.\d+)+/',
                            'message' => 'Version must be in correct format: 1.0.0 or 1.0.0.0'
                        ]
                    )
                ]
            ]
        );

        $this->addElement(
            'text',
            'author',
            [
                'label' => 'Author',
                'description' => 'Who create this package? Identify youself!'
            ]
        );

        $this->addElement(
            'text',
            'website',
            [
                'label' => 'Website',
                'description' => 'Where user will look for new version?'
            ]
        );

        $this->addElement(
            'textArea',
            'header',
            [
                'label' => 'Header comments',
                'description' => 'This text will be placed in each file of package. Use comment block /**  **/.'
            ]
        );

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', ['for' => 'admin-packages']);

    }

    /**
     * Validates the form.
     *
     * @param array         $data               Data to validate.
     * @param AbstractModel $entity             Entity to validate.
     * @param bool          $skipEntityCreation Skip entity creation.
     *
     * @return boolean
     */
    public function isValid($data = null, $entity = null, $skipEntityCreation = false)
    {
        // Check package location.
        $packageManager = new Manager();
        $path = $packageManager->getPackageLocation($data['type']);
        if (!is_writable($path)) {
            $this->addError('Can not create package. Package location isn\'t writable: ' . $path);

            return false;
        }

        // Also check that config file is writable.
        if (!is_writable(ROOT_PATH . Application::SYSTEM_CONFIG_PATH)) {
            $this->addError('Configuration file isn\'t writable...');

            return false;
        }

        if (!parent::isValid($data, $entity, $skipEntityCreation)) {
            return false;
        }

        // Check package existence.
        /** @var \Phalcon\Mvc\Model\Query\Builder $query */
        $query = $this->getDI()->get('modelsManager')->createBuilder()
            ->from(['t' => '\Core\Model\Package'])
            ->where('t.type = :type: AND t.name = :name:', ['type' => $data['type'], 'name' => $data['name']]);

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