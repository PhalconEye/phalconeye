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
 * Check content is json.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Validator
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class JsonValidator extends Validator implements ValidatorInterface
{
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
        /** @var Request $request */
        $request = Di::getDefault()->get('request');

        /** @var Request\File $file */
        foreach ($request->getUploadedFiles(true) as $file) {
            if ($file->getKey() != $attribute) {
                continue;
            }

            $content = file_get_contents($file->getTempName());
            $result = $this->_isJson($content);

            if (!$result) {
                $validator->appendMessage(
                    new Validation\Message('Please, provide correct JSON file.', $attribute)
                );
            }

            return $result;
        }

        return false;
    }

    /**
     * Check string is json.
     *
     * @param $string String to check.
     *
     * @return bool Check result.
     */
    private function _isJson($string) : bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}