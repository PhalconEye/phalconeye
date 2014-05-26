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
     * Form current method.
     *
     * @var string
     */
    protected $_method = self::METHOD_GET;

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $loremShort = 'Lorem ipsum dolor sit amet';
        $loremMedium = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt
                        ut labore et dolore magna aliqua.';
        $loremLong = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut '.
                     'labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco '.
                     'laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in '.
                     'voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat '.
                     'non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        $this->setTitle('Robots factory');
        $this->setDescription($loremMedium);

        /**
         * Text elements
         */
        $this->addContentFieldSet()
            ->addText('robot1', 'Robot name', null, 'Wall-E')
            ->addText('robot1', 'Robot code', $loremShort, 'CRA 25 CALLE 100')
            ->addPassword(
                'password',
                'Password',
                $loremMedium,
                [],
                ['placeholder' => 'Provide master password']
            )
            ->addTextArea('desc', 'Skills', $loremMedium, $loremLong);

        /**
         * Control elements
         */
        $this->addContentFieldSet('Control elements')
            ->addSelect('color', 'Color', null, ['Rusty orange', 'Snow white', 'Carbon black'])
            ->addMultiSelect('parts', 'Parts', $loremMedium, ['Head', 'Body', 'Arms', 'Legs', 'CPU'], [0, 1, 2, 3, 4])
            ->addCheckbox('laser', 'Add laser', null, 1, true)
            ->addCheckbox('parachute', 'Add parachute', $loremShort, 1)
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
             ->addFile('schema', 'Scheme', 'Upload electric circuits scheme')
             ->addFile('image', 'Image', 'Upload image', true, 'files/demo/robot.png')
             ->addRemoteFile('remote', 'Image', 'Remote image', 'files/demo/robot.png');

        /**
         * Footer
         */
        $this->addFooterFieldSet()
            ->addButtonLink('link', 'ButtonLink')
            ->addButton('submit', 'Button');

    }
}