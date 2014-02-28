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

namespace Core\Form\Admin\Package;

use Core\Form\FileForm;
use Engine\Form\Validator\MimeType;

/**
 * Upload package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Upload extends FileForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this
            ->setTitle('Install new package')
            ->setDescription('Select package you want to install (zip extension).');

        $this->addContentFieldSet()
            ->addFile('package')
            ->getValidation()
            ->add('package', new MimeType(['type' => 'application/zip']));

        $this->addFooterFieldSet()
            ->addButton('upload')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'admin-packages']);
    }
}