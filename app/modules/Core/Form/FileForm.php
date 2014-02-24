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

namespace Core\Form;

use Engine\Form\AbstractForm;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Exception;
use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form\Behaviour\FormBehaviour;
use Engine\Form;
use Phalcon\Filter;
use Phalcon\Http\Request\FileInterface;
use Phalcon\Tag as Tag;
use Phalcon\Translate;
use Phalcon\Validation;

/**
 * File form class.
 *
 * @category  PhalconEye
 * @package   Core\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class FileForm extends CoreForm
{
    /**
     * Form current encryption type.
     *
     * @var string
     */
    protected $_enctype = AbstractForm::ENCTYPE_MULTIPART;

    /**
     * Image transformations.
     *
     * @var array
     */
    protected $_transformations = [];

    /**
     * Check if there is uploaded files.
     *
     * @param string|null $name          Field name.
     * @param bool|null   $withoutErrors Get files without errors.
     *
     * @return bool
     */
    public function hasFiles($name = null, $withoutErrors = true)
    {
        if (!$name) {
            return $this->getDI()->get('request')->hasFiles($withoutErrors);
        }

        return (bool)$this->getFiles($name, $withoutErrors);
    }

    /**
     * Get uploaded files.
     *
     * @param string|null $name          Field name.
     * @param bool|null   $withoutErrors Get files without errors.
     *
     * @return FileInterface|null
     */
    public function getFiles($name = null, $withoutErrors = true)
    {
        $files = $this->getDI()->get('request')->getUploadedFiles($withoutErrors);
        if (!$name) {
            return $files;
        }

        foreach ($files as $file) {
            if ($file->getKey() == $name) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Set image transformation options.
     *
     * @param string $name    Field name.
     * @param array  $options Image adapter options.
     *
     * @return $this
     */
    public function setImageTransformation($name, $options)
    {
        $this->_transformations[$name] = $options;
        return $this;
    }

    /**
     * Check form validation and transform image if valid.
     *
     * @param array|null $data               Form data.
     * @param bool       $skipEntityCreation Skip entity creation.
     *
     * @throws \Engine\Exception
     * @return bool
     */
    public function isValid($data = null, $skipEntityCreation = false)
    {
        $isValid = parent::isValid($data, $skipEntityCreation);

        if ($isValid) {
            foreach ($this->_transformations as $field => $transform) {
                $file = $this->getFiles($field);

                if (!$file) {
                    continue;
                }

                $adapterClass = 'Phalcon\Image\Adapter\\' . $transform['adapter'];
                unset($transform['adapter']);
                if (!class_exists($adapterClass)) {
                    throw new Exception(sprintf('Image adapter "%s" does not exists.', $adapterClass));
                }

                $adapter = new $adapterClass($file->getTempName());
                foreach ($transform as $option => $values) {
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    call_user_func_array([$adapter, $option], $values);
                }

                $fileName = $file->getTempName() . '.' . pathinfo($file->getName(), PATHINFO_EXTENSION);

                if (!$adapter->save($fileName)) {
                    $this->getDI()->getLogger()->error(
                        sprintf('Can not transform image. Form: "%s", Field: "%s".', get_class($this), $field)
                    );
                } else {
                    rename($fileName, $file->getTempName());
                }
            }
        }

        return $isValid;
    }
}