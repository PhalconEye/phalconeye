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

class Validator_Between extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{
     /**
     * Executes the validation
     *
     * @param Phalcon\Validation $validator
     * @param string $attribute
     * @return boolean
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $min = $this->getOption('min');
        $max = $this->getOption('max');

        $valid = true;

        if ($min && $value <= $min){
            $valid = false;
        }

        if ($max && $value >= $min){
            $valid = false;
        }

        if (!$valid){
            $message = $this->getOption('message');
            if (!$message) {
                $message = "Value of field '{$attribute}' is not between '{$min}' and '{$max}'.";
            }

            $validator->appendMessage(new \Phalcon\Validation\Message($message, $attribute, 'Ip'));
        }

        return $valid;
    }


}
