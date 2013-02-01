<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 */

class Validator_StringLength extends Validator_Abstract
{
    const INVALID   = 'stringLengthInvalid';
    const TOO_SHORT = 'stringLengthTooShort';
    const TOO_LONG  = 'stringLengthTooLong';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given, value should be a string",
        self::TOO_SHORT => "'%value%' is less than %min% characters long",
        self::TOO_LONG  => "'%value%' is greater than %max% characters long"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_min',
        'max' => '_max'
    );

    /**
     * Minimum length
     *
     * @var integer
     */
    protected $_min;

    /**
     * Maximum length
     *
     * If null, there is no maximum length
     *
     * @var integer|null
     */
    protected $_max;

    /**
     * Encoding to use
     *
     * @var string|null
     */
    protected $_encoding;

    /**
     * Sets validator options
     *
     * @param  integer $min
     * @param  integer $max
     * @param  string $encoding
     */
    public function __construct($min = 0, $max = null, $encoding = null)
    {
        $this->setMin($min);
        $this->setMax($max);
        $this->setEncoding($encoding);
    }

    /**
     * Returns the min option
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * Sets the min option
     *
     * @param  integer $min
     * @throws Validator_Exception
     * @return Validator_StringLength Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->_max && $min > $this->_max) {
            /**
             * @see Validator_Exception
             */
            // require_once 'Zend/Validate/Exception.php';
            throw new Validator_Exception("The minimum must be less than or equal to the maximum length, but $min >"
                                            . " $this->_max");
        }
        $this->_min = max(0, (integer) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return integer|null
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * Sets the max option
     *
     * @param  integer|null $max
     * @throws Validator_Exception
     * @return Validator_StringLength Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->_max = null;
        } else if ($max < $this->_min) {
            /**
             * @see Validator_Exception
             */
            // require_once 'Zend/Validate/Exception.php';
            throw new Validator_Exception("The maximum must be greater than or equal to the minimum length, but "
                                            . "$max < $this->_min");
        } else {
            $this->_max = (integer) $max;
        }

        return $this;
    }

    /**
     * Returns the actual encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Sets a new encoding to use
     *
     * @param string $encoding
     * @throws Validator_Exception
     * @return Validator_StringLength
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            $orig   = iconv_get_encoding('internal_encoding');
            $result = iconv_set_encoding('internal_encoding', $encoding);
            if (!$result) {
                // require_once 'Zend/Validate/Exception.php';
                throw new Validator_Exception('Given encoding not supported on this OS!');
            }

            iconv_set_encoding('internal_encoding', $orig);
        }

        $this->_encoding = $encoding;
        return $this;
    }

    /**
     * Defined by Validator_Interface
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        if ($this->_encoding !== null) {
            $length = iconv_strlen($value, $this->_encoding);
        } else {
            $length = iconv_strlen($value);
        }

        if ($length < $this->_min) {
            $this->_error(self::TOO_SHORT);
        }

        if (null !== $this->_max && $this->_max < $length) {
            $this->_error(self::TOO_LONG);
        }

        if (count($this->_messages)) {
            return false;
        } else {
            return true;
        }
    }
}
