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

namespace Core\Form\Admin\Menu;

use Core\Model\Language;
use Core\Model\MenuItem;
use Engine\Db\AbstractModel;
use Engine\Form;
use User\Model\Role;

/**
 * Create menu item.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Menu
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CreateItem extends Form
{
    /**
     * Form constructor.
     *
     * @param null|AbstractModel $model Model object.
     */
    public function __construct($model = null)
    {
        if ($model === null) {
            $model = new MenuItem();
        }

        $model->prepareRoles();
        $model->prepareLanguages();

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
            ->setOption('description', "This menu item will be available under menu or parent menu item.");


        $this->addElement('text', 'title', ['label' => 'Title']);


        $this->addElement(
            'select',
            'target',
            [
                'label' => 'Title',
                'description' => 'Link type',
                'options' => [
                    null => 'Default link',
                    '_blank' => 'Opens the linked document in a new window or tab',
                    '_parent' => 'Opens the linked document in the parent frame',
                    '_top' => 'Opens the linked document in the full body of the window',
                ]
            ]
        );

        $this->addElement(
            'radio',
            'url_type',
            [
                'label' => 'Select url type',
                'options' => [
                    0 => 'Url',
                    1 => 'System page'
                ],
                'value' => 0
            ]
        );

        $this->addElement('text', 'url', ['label' => 'Url']);

        $this->addElement(
            'text',
            'page',
            [
                'label' => 'Page',
                'description' => 'Start typing to see pages variants.',
                'data-link' => $this->di->get('url')->get('admin/pages/suggest'),
                'data-target' => '#page_id',
                'autocomplete' => 'off',
                'data-autocomplete' => 'true',
            ]
        );

        $this->addElement(
            'textArea',
            'onclick',
            [
                'label' => 'Onclick',
                'description' => 'Type JS action that will be performed when this menu item is selected.'
            ]
        );

        $this->addElement(
            'textArea',
            'tooltip',
            [
                'label' => 'Tooltip'
            ]
        );

        $this->addElement(
            'html',
            'ckeditor',
            [
                'ignore' => true,
                'html' =>
                    '<script type="text/javascript">
                    $(document).ready(setTimeout(function () {CKEDITOR.replace("tooltip");}, 300));
                    </script>'
            ],
            1000
        );

        $this->addElement(
            'select',
            'tooltip_position',
            [
                'label' => 'Tooltip position',
                'options' => [
                    'top' => 'Top',
                    'bottom' => 'Bottom',
                    'left' => 'Left',
                    'right' => 'Right'
                ]
            ]
        );

        $this->addElement(
            'remoteFile',
            'icon',
            [
                'label' => 'Icon',
                'title' => $this->di->get('trans')->_('Select file')
            ]
        );

        $this->addElement(
            'select',
            'icon_position',
            [
                'label' => 'Icon position',
                'options' => [
                    'left' => 'Left',
                    'right' => 'Right'
                ]
            ]
        );

        $this->addElement(
            'select',
            'languages',
            [
                'label' => 'Languages',
                'description' =>
                    'Choose the language in which the menu item will be displayed.
                    If no one selected - will be displayed at all.',
                'options' => Language::find(),
                'using' => ['locale', 'name'],
                'multiple' => 'multiple'
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

        $this->addElement('hidden', 'page_id');
        $this->addElement('hidden', 'menu_id');
        $this->addElement('hidden', 'parent_id');
    }
}