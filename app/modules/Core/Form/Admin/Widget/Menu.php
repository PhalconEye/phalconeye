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

namespace Core\Form\Admin\Widget;

use Core\Form\CoreForm;

/**
 * Menu widget admin form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Menu extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setDescription('Select menu that will be rendered.');

        $this->addContentFieldSet()
            ->addText('title')
            ->addText('class', 'Menu css class')
            ->addText(
                'menu',
                'Menu',
                'Start typing to see menus variants',
                null,
                [],
                [
                    'data-link' => $this->getDI()->getUrl()->get('admin/menus/suggest'),
                    'data-target' => '#menu_id',
                    'data-widget' => 'autocomplete',
                    'autocomplete' => 'off'
                ]
            )
            ->addHidden('menu_id');
    }
}