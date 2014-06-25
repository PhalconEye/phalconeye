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

namespace Engine\Form\Validator;

use Phalcon\DI;
use Phalcon\Http\Request;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

/**
 * Form validator - Image.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Validator
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Image extends Validator implements ValidatorInterface
{
    /**
     * Current validation object.
     *
     * @var Validation
     */
    protected $_currentValidator;

    /**
     * Current field name.
     *
     * @var string
     */
    protected $_currentAttribute;

    /**
     * Executes the validation
     *
     * @param Validation $validator Validator object.
     * @param string     $attribute Attribute name.
     *
     * @return Group
     */
    public function validate($validator, $attribute)
    {
        $this->_currentValidator = $validator;
        $this->_currentAttribute = $attribute;

        /** @var Request $request */
        $request = DI::getDefault()->get('request');
        $isValid = true;

        if ($request->hasFiles(true)) {
            foreach ($request->getUploadedFiles(true) as $file) {
                if ($file->getKey() != $attribute) {
                    continue;
                }

                $size = getimagesize($file->getTempName());
                if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
                    $this->_addMessage('Can not detect size of "%s" image.', $attribute);
                    $isValid = false;
                    continue;
                }

                $width = $size[0];
                $height = $size[1];

                if ($this->isSetOption('max-width') && $this->getOption('max-width') < $width) {
                    $this->_addMessage('Wrong width "%s". Max width: "%s"', $width, $this->getOption('max-width'));
                    $isValid = false;
                    continue;
                }

                if ($this->isSetOption('min-width') && $this->getOption('min-width') > $width) {
                    $this->_addMessage('Wrong width "%s". Min width: "%s"', $width, $this->getOption('min-width'));
                    $isValid = false;
                    continue;
                }

                if ($this->isSetOption('max-height') && $this->getOption('max-height') < $height) {
                    $this->_addMessage('Wrong height "%s". Max height: "%s"', $height, $this->getOption('max-height'));
                    $isValid = false;
                    continue;
                }

                if ($this->isSetOption('min-height') && $this->getOption('min-height') > $height) {
                    $this->_addMessage('Wrong height "%s". Min height: "%s"', $height, $this->getOption('min-height'));
                    $isValid = false;
                    continue;
                }
            }
        }

        return $isValid;
    }

    /**
     * Add error message.
     *
     * @param string     $msg  Message text.
     * @param array|null $args Message params.
     *
     * @return void
     */
    protected function _addMessage($msg, $args = null)
    {
        $this->_currentValidator->appendMessage(
            new Validation\Message(vsprintf($msg, $args), $this->_currentAttribute)
        );
    }
}