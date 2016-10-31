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

namespace Core\Form\Backoffice\Package\FieldSet;

use Core\Model\PackageModel;
use Engine\Form\FieldSet;
use Engine\Package\PackageGenerator;

/**
 * Widget package fieldset.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package\FieldSet
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageWidgetFieldSet extends FieldSet
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setLegend('Widget information');
        $modules = PackageModel::findByType(PackageGenerator::PACKAGE_TYPE_MODULE, true);
        $widgetModules = [null => 'No'];
        foreach ($modules as $module) {
            $widgetModules[$module->name] = $module->title;
        }

        $this
            ->addSelect('module', 'Is related to module?', null, $widgetModules)
            ->addCheckbox(
                'is_paginated',
                'Is Paginated?',
                'If this enabled - widget will has additional control
                    enabled for allowed per page items count selection in admin form',
                1,
                false,
                0
            )
            ->addCheckbox(
                'is_acl_controlled',
                'Is ACL controlled?',
                'If this enabled - widget will has additional control
                    enabled for allowed roles selection in admin form',
                1,
                false,
                0
            )
            ->addSelect(
                'admin_form',
                'Admin form',
                'Does this widget have some controlling form?',
                [
                    null => 'No',
                    'action' => 'Action',
                    'form_class' => 'Form class'
                ]
            )
            ->addText('form_class', 'Form class', 'Enter existing form class')
            ->addCheckbox('enabled', 'Enabled?', null, 1, false, 0);

        $this->setCondition('form_class', 'admin_form', 'form_class');
    }
}