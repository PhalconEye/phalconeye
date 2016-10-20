<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Form\Backoffice\Menu;

use Core\Form\CoreForm;
use Core\Model\LanguageModel;
use Core\Model\MenuItemModel;
use Engine\Db\AbstractModel;
use Engine\Form\FieldSet;
use User\Model\RoleModel;

/**
 * Create menu item.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Menu
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MenuItemCreateForm extends CoreForm
{
    /**
     * Create form.
     *
     * @param AbstractModel $entity Entity object.
     */
    public function __construct(AbstractModel $entity = null)
    {
        parent::__construct();

        if (!$entity) {
            $entity = new MenuItemModel();
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
        $this->setDescription('This menu item will be available under menu or parent menu item.');

        $content = $this->addContentFieldSet()
            ->addText('title')
            ->addSelect(
                'target',
                'Target',
                'Link type',
                [
                    null => 'Default link',
                    MenuItemModel::ITEM_TARGET_BLANK => 'Opens the linked document in a new window or tab',
                    MenuItemModel::ITEM_TARGET_PARENT => 'Opens the linked document in the parent frame',
                    MenuItemModel::ITEM_TARGET_TOP => 'Opens the linked document in the full body of the window',
                ]
            )
            ->addRadio('url_type', 'Select url type', null, [0 => 'Url', 1 => 'System page'])
            ->addText('url', 'Url', 'Do not type url with starting slash... Example: "somepage/url/to?param=1"')
            ->addText(
                'page',
                'Page',
                'Start typing to see pages variants.',
                null,
                [],
                [
                    'data-link' => $this->getDI()->getUrl()->get('admin/pages/suggest'),
                    'data-target' => '#page_id',
                    'data-widget' => 'autocomplete',
                    'autocomplete' => 'off'
                ]
            )
            ->addTextArea(
                'onclick',
                'OnClick',
                'Type JS action that will be performed when this menu item is selected.'
            )
            ->addCkEditor('tooltip')
            ->addSelect(
                'tooltip_position',
                'Tooltip position',
                null,
                [
                    MenuItemModel::ITEM_TOOLTIP_POSITION_TOP => 'Top',
                    MenuItemModel::ITEM_TOOLTIP_POSITION_BOTTOM => 'Bottom',
                    MenuItemModel::ITEM_TOOLTIP_POSITION_LEFT => 'Left',
                    MenuItemModel::ITEM_TOOLTIP_POSITION_RIGHT => 'Right'
                ]
            )
            ->addRemoteFile('icon', 'Select icon')
            ->addSelect(
                'icon_position',
                'Icon position',
                null,
                [
                    MenuItemModel::ITEM_ICON_POSITION_LEFT => 'Left',
                    MenuItemModel::ITEM_ICON_POSITION_RIGHT => 'Right'
                ]
            )
            ->addMultiSelect(
                'languages',
                'Languages',
                'Choose the language in which the menu item will be displayed.
                    If no one selected - will be displayed at all.',
                LanguageModel::find(),
                null,
                ['using' => ['language', 'name']]
            )
            ->addMultiSelect(
                'roles',
                'Roles',
                'If no value is selected, will be allowed to all (also as all selected).',
                RoleModel::find(),
                null,
                ['using' => ['id', 'name']]
            )
            ->addCheckbox('is_enabled', 'Is enabled', null, 1, true, false)
            ->addHidden('page_id')
            ->addHidden('menu_id')
            ->addHidden('parent_id');

        $this->_setValidation($content);
    }

    /**
     * Set form validation.
     *
     * @param FieldSet $content Content object.
     *
     * @return void
     */
    protected function _setValidation($content)
    {
        $content->setRequired('title');
    }
}