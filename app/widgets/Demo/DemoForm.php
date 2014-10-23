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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Widget\Demo;

use Core\Form\CoreForm;

/**
 * Demo widget form.
 *
 * @category  PhalconEye
 * @package   Widget\Demo
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class DemoForm extends CoreForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $loremShort = 'Lorem ipsum dolor sit amet';
        $loremMedium = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt' .
            'ut labore et dolore magna aliqua. ';

        $this->setTitle('Robots factory');
        $this->setDescription($loremMedium);

        /**
         * Text elements
         */
        $this->addContentFieldSet()
            ->setAttribute('id', 'form_content_text')

            ->addText(
                'robot_name',
                'Robot name',
                null, 'Wall-E',
                ['required' => true]
            )

            ->addText(
                'robot_alias[]',
                'Robot aliases',
                $loremMedium,
                ['CXB-250 T-500', 'CXB-250 T-500-PRO', 'CXB-250 T-1000', 'CXB-250 T-1000-PRO'],
                ['dynamic' => ['min' => 1, 'max' => 4]]
            )

            ->addPassword(
                'password',
                'Master Password',
                $loremShort,
                [],
                ['placeholder' => 'Provide master password']
            )

            ->addPassword(
                'spare_passwords[]',
                'Spare Passwords',
                $loremShort,
                [
                    'placeholder' => 'Provide spare password',
                    'dynamic' => ['min' => 1, 'max' => 4]
                ]
            )

            ->addTextArea(
                'desc',
                'Description',
                $loremMedium,
                $loremMedium . $loremMedium . $loremMedium
            )

            ->addTextArea(
                'skills[]',
                'Additional Skills',
                $loremMedium,
                null,
                ['dynamic' => ['min' => 1, 'max' => 3]]
            );


        /**
         * Control elements
         */
        $this->addContentFieldSet('Control elements')
            ->setAttribute('id', 'form_content_controls')

            ->addSelect(
                'color',
                'Main color',
                null,
                ['Rusty orange', 'Snow white', 'Carbon black']
            )

            ->addSelect(
                'colors[]',
                'Secondary colors',
                $loremMedium,
                ['Red', 'Green', 'Blue'],
                [0, 1, 2],
                ['dynamic' => ['min' => 1, 'max' => 3]]
            )

            ->addMultiSelect(
                'parts',
                'Parts',
                null,
                ['Head', 'Body', 'Arms', 'Legs', 'CPU'],
                [0, 1, 2, 3, 4]
            )

            ->addMultiSelect(
                'spare_parts[]',
                'Spare parts',
                $loremMedium,
                ['Head', 'Body', 'Arms', 'Legs', 'CPU'],
                [[0], [1, 2], [3, 4]],
                ['dynamic' => ['min' => 2, 'max' => 5]]
            )

            ->addCheckbox(
                'laser',
                'Add laser',
                null,
                1,
                true
            )

            ->addCheckbox(
                'parachute',
                'Add parachute',
                $loremShort,
                1
            )

            ->addRadio(
                'cpu',
                'CPU',
                null,
                [
                    '8008 8-bit 0.8 MHz',
                    'Sparc 16-bit 800 MHz',
                    'Corex2 64-bit 2 GHz',
                    'Atom-XI 128-bit 0.5 THz',
                ]
            )

            ->addMultiCheckbox(
                'tuning',
                'Tuning',
                'Select extra enhancements',
                [
                    '+ 128 MB L2 Operation Memory',
                    '+ 50 TB SSD-HLD Storage',
                    '+ NOS (Nitrous Oxide Systems) Kit'
                ]
            );

        /**
         * File elements
         */
        $this->addContentFieldSet('File elements')
            ->setAttribute('id', 'form_content_files')

            ->addFile(
                'scheme',
                'Scheme',
                'Upload electric circuits scheme'
            )

            ->addFile(
                'schemas[]',
                'Extra schemes',
                'Upload up to 10 extra electric circuits schemes',
                false,
                null,
                ['dynamic' => ['min' => 1, 'max' => 10]]
            )

            ->addFile(
                'image',
                'Image',
                'Upload image',
                true,
                'files/demo/robot.png'
            )

            ->addFile(
                'images[]',
                'Even more images',
                'Upload even more images',
                true,
                null,
                ['dynamic' => ['min' => 1, 'max' => 3]]
            )

            ->addRemoteFile(
                'remote',
                'Server image',
                null,
                'files/demo/robot.png'
            )

            ->addRemoteFile(
                'remotes[]',
                'Even more server images',
                null,
                null,
                ['dynamic' => ['min' => 1, 'max' => 3]]
            );

        /**
         * Footer
         */
        $this->addFooterFieldSet()
            ->addButton('default', 'DefaultButton', false)
            ->addButton('success', 'SuccessButton', false, null, [], ['class' => 'btn btn-success'])
            ->addButton('danger', 'DangerButton', false, null, [], ['class' => 'btn btn-danger'])
            ->addButton('submit', 'PrimaryButton (aKa SubmitButton)')
            ->addButtonLink('link', 'LinkButton');
    }
}