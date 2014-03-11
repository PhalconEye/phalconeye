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

namespace Core\Form\Admin\Menu;

use Core\Form\CoreForm;
use Core\Model\Language;
use Core\Model\MenuItem;
use Engine\Db\AbstractModel;
use Engine\Form\FieldSet;
use User\Model\Role;

/**
 * Create menu item.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Menu
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CreateItem extends CoreForm
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
            $entity = new MenuItem();
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
                    MenuItem::ITEM_TARGET_BLANK => 'Opens the linked document in a new window or tab',
                    MenuItem::ITEM_TARGET_PARENT => 'Opens the linked document in the parent frame',
                    MenuItem::ITEM_TARGET_TOP => 'Opens the linked document in the full body of the window',
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
                    MenuItem::ITEM_TOOLTIP_POSITION_TOP => 'Top',
                    MenuItem::ITEM_TOOLTIP_POSITION_BOTTOM => 'Bottom',
                    MenuItem::ITEM_TOOLTIP_POSITION_LEFT => 'Left',
                    MenuItem::ITEM_TOOLTIP_POSITION_RIGHT => 'Right'
                ]
            )
            ->addRemoteFile('icon', 'Select icon')
            ->addSelect(
                'icon_position',
                'Icon position',
                null,
                [
                    MenuItem::ITEM_ICON_POSITION_LEFT => 'Left',
                    MenuItem::ITEM_ICON_POSITION_RIGHT => 'Right'
                ]
            )
            ->addMultiSelect(
                'languages',
                'Languages',
                'Choose the language in which the menu item will be displayed.
                    If no one selected - will be displayed at all.',
                Language::find(),
                null,
                ['using' => ['language', 'name']]
            )
            ->addMultiSelect(
                'roles',
                'Roles',
                'If no value is selected, will be allowed to all (also as all selected).',
                Role::find(),
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