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
 * Header widget admin form.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Widget
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Header extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setDescription('Settings for header of you site.');

        $this->addContentFieldSet()
            ->addRemoteFile('logo', 'Logo image (url)')
            ->addCheckbox('show_title', 'Show site title', null, 1, false, 0)
            ->addCheckbox('show_auth', 'Show authentication links (logo, register, logout, etc)', null, 1, false, 0);
    }
}