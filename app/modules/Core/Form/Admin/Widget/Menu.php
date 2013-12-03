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

namespace Core\Form\Admin\Widget;

use Engine\Form;

/**
 * Menu widget admin form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Menu extends Form
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('description', "Select menu that will be rendered.");

        $this->addElement('text', 'title', array(
            'label' => 'Title'
        ));

        $this->addElement('text', 'class', array(
            'label' => 'Menu css class'
        ));

        $this->addElement('text', 'menu', array(
            'label' => 'Menu',
            'description' => 'Start typing to see menus variants',
            'data-link' => $this->di->get('url')->get('admin/menus/suggest'),
            'data-target' => '#menu_id',
            'autocomplete' => 'off',
            'data-autocomplete' => 'true',
        ));


        $this->addElement('hidden', 'menu_id');
    }
}