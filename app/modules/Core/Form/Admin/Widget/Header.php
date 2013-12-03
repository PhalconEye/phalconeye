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
 * Header widget admin form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Header extends Form
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function init()
    {
        $this
            ->setOption('description', "Settings for header of you site.");

        $this->addElement('RemoteFile', 'logo', array(
            'label' => 'Logo image (url)'
        ));

        $this->addElement('check', 'show_title', array(
            'label' => 'Show site title',
            'options' => 1
        ));

        $this->addElement('check', 'show_auth', array(
            'label' => 'Show authentication links (logo, register, logout, etc)',
            'options' => 1
        ));

    }
}