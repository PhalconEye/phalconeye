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

namespace Engine\Form\Validator;

use Phalcon\DI;
use Phalcon\Http\Request;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

/**
 * Form validator - Mime.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Validator
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class MimeTypeValidator extends Validator implements ValidatorInterface
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
     * @return bool
     */
    public function validate(Validation $validator, $attribute)
    {
        $this->_currentValidator = $validator;
        $this->_currentAttribute = $attribute;

        /** @var Request $request */
        $request = DI::getDefault()->get('request');
        $isValid = true;

        if ($request->hasFiles(true)) {
            $fInfo = finfo_open(FILEINFO_MIME_TYPE);
            $types = [];

            if ($this->hasOption('type')) {
                $types = $this->getOption('type');
                if (!is_array($types)) {
                    $types = [$types];
                }
            }

            foreach ($request->getUploadedFiles(true) as $file) {
                if ($file->getKey() != $attribute) {
                    continue;
                }

                if (!empty($types)) {
                    $mime = finfo_file($fInfo, $file->getTempName());
                    if (!in_array($mime, $types)) {
                        $isValid = false;
                        $this->_addMessage(
                            'Incorrect file type (%s), allowed types: %s',
                            [$mime, implode(',', $types)]
                        );
                    }
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