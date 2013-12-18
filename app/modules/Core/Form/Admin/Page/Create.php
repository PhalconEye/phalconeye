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

namespace Core\Form\Admin\Page;

use Core\Model\Page;
use Engine\Db\AbstractModel;
use Engine\Form;
use User\Model\Role;

/**
 * Create page.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Page
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
            $model = new Page();
        }
        $model->prepareRoles();

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
            ->setOption('title', "Page Creation")
            ->setOption('description', "Create new page.");

        $this->addElement('text', 'title', ['label' => 'Title']);

        $this->addElement(
            'text',
            'url',
            [
                'label' => 'Url',
                'description' => 'Page will be available under http://' . $_SERVER['HTTP_HOST'] . '/page/[URL NAME]'
            ]
        );

        $this->addElement('textArea', 'description', ['label' => 'Description']);
        $this->addElement('textArea', 'keywords', ['label' => 'Keywords']);

        $this->addElement(
            'text',
            'controller',
            [
                'label' => 'Controller',
                'description' =>
                    'Controller and action name that will handle this page. Example: NameController->someAction'
            ]
        );

        $this->addElement(
            'select',
            'roles',
            [
                'label' => 'Roles',
                'description' => 'If no value is selected, will be allowed to all (also as all selected).',
                'options' => Role::find(),
                'using' => ['id', 'name'],
                'multiple' => 'multiple'
            ]
        );

        $this->addButton('Create', true);
        $this->addButtonLink('Cancel', ['for' => 'admin-pages']);
    }
}