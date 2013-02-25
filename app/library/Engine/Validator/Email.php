<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class Validator_Email extends Validator_Abstract
{
    const INVALID = 'stringLengthInvalid';

    const REGEX = "^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]$";

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid email given.",
    );

    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->_error(self::INVALID);
            return false;
        }

        return true;

    }
}
