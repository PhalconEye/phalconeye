<?php

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
