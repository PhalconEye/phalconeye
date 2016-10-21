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

namespace Core\Form\Backoffice\Package;

use Core\Form\FileForm;
use Engine\Form\Validator\MimeTypeValidator;

/**
 * Upload package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageUploadForm extends FileForm
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
            ->add('package', new MimeTypeValidator(['type' => 'application/zip']));

        $this->addFooterFieldSet()
            ->addButton('upload')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-packages']);
    }
}