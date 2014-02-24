<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

use Core\Form\Admin\Package\FieldSet\Widget;
use Core\Form\CoreForm;
use Core\Model\Package;
use Core\Model\Widget as WidgetModel;
use Engine\Config;
use Engine\Db\AbstractModel;
use Engine\Form\Validator\Regex;
use Engine\Package\Manager;

/**
 * Create package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Create extends CoreForm
{
    const
        /**
         * Widget fieldset name.
         */
        FIELDSET_WIDGET = 'widget_info';

    /**
     * Create form.
     *
     * @param AbstractModel $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new Package();
        }

        $this->addEntity($entity);
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Package Creation')
            ->setDescription('Create new package.');

        $this->_addElements();
        $this->_setConditions();
        $this->_setValidation();
        $this->_addButtons();
    }

    /**
     * Add main package form elements.
     *
     * @return void
     */
    protected function _addElements()
    {
        $this->addContentFieldSet()
            ->addText('name', 'Name', 'Name must be in lowercase and contains only letters.')
            ->addSelect('type', 'Package type', null, Manager::$allowedTypes)
            ->addText('title')
            ->addTextArea('description')
            ->addText('version', 'Version', 'Type package version. Ex.: 0.5.7')
            ->addText('author', 'Author', 'Who create this package? Identify yourself!')
            ->addText('website', 'Website', 'Where user will look for new version?')
            ->addTextArea(
                'header',
                'Header comments',
                'This text will be placed in each file of package. Use comment block /**  **/.'
            );

        $this->addFieldSet(new Widget(self::FIELDSET_WIDGET));
    }

    /**
     * Set elements conditions.
     *
     * @return void
     */
    protected function _setConditions()
    {
        $this->setFieldSetCondition(self::FIELDSET_WIDGET, 'type', 'widget');
    }

    /**
     * Set elements validation.
     *
     * @return void
     */
    protected function _setValidation()
    {
        $fieldSet = $this->getFieldSet(self::FIELDSET_CONTENT);
        $fieldSet->getValidation()
            ->add(
                'name',
                new Regex(
                    [
                        'pattern' => '/[a-z]+/',
                        'message' => 'Name must be in lowercase and contains only letters.'
                    ]
                )
            )
            ->add(
                'version',
                new Regex(
                    [
                        'pattern' => '/\d+(\.\d+)+/',
                        'message' => 'Version must be in correct format: 1.0.0 or 1.0.0.0'
                    ]
                )
            );
    }

    /**
     * Add form buttons.
     */
    protected function _addButtons()
    {
        $this->addFooterFieldSet()
            ->addButton('create')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-packages']);
    }

    /**
     * Validates the form.
     *
     * @param array $data               Data to validate.
     * @param bool  $skipEntityCreation Skip entity creation.
     *
     * @return boolean
     */
    public function isValid($data = null, $skipEntityCreation = true)
    {
        if (!$data) {
            $data = $this->getDI()->getRequest()->getPost();
        }

        // Check package location.
        $packageManager = new Manager();
        $path = $packageManager->getPackageLocation($data['type']);
        if (!is_writable($path)) {
            $this->addError('Can not create package. Package location isn\'t writable: ' . $path);
            $this->setValues($data);
            return false;
        }

        // Also check that config file is writable.
        if (!is_writable(ROOT_PATH . Config::CONFIG_PATH)) {
            $this->addError('Configuration file isn\'t writable...');
            $this->setValues($data);
            return false;
        }

        if (isset($data['type']) && $data['type'] == 'widget' && !$this->hasEntity('widget')) {
            $this->addEntity(new WidgetModel(), 'widget');
        }

        if (!parent::isValid($data, $skipEntityCreation)) {
            return false;
        }

        // Check package existence.
        $id = $this->getEntity()->id;
        $condition = "type='{$data['type']}' AND name='{$data['name']}'" . ($id ? " AND id!='{$id}'" : '');
        if (Package::findFirst($condition)) {
            $this->addError('Package with that name already exist!');
            return false;
        }

        // Check widget existence.
        if ($this->hasEntity('widget')) {
            $name = ucfirst($data['name']);
            $id = $this->getEntity('widget')->id;
            $condition = "module='{$data['module']}' AND name='{$name}'" . ($id ? " AND id!='{$id}'" : '');

            if (WidgetModel::findFirst($condition)) {
                $this->addError('Widget with that name already exist!');
                return false;
            }
        }

        return true;
    }
}