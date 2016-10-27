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
 | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
 +------------------------------------------------------------------------+
*/

namespace Core\Command\Validation;

use Engine\Behaviour\DIBehaviour;
use Phalcon\Di;
use Phalcon\Validation;
use Phalcon\Validation\Validator;

/**
 * Module existence validation for widget in module.
 *
 * @category  PhalconEye
 * @package   Core\Commands\Validation
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class WidgetModuleValidator extends Validator
{
    private $_di = null;

    /**
     * Validation constructor
     *
     * @param DIBehaviour|Di $di Dependency injection.
     */
    public function __construct($di)
    {
        $this->_di = $di;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);
        if (!empty($value)) {
            $result = array_key_exists($value, $this->_di->getRegistry()->modules) &&
                !array_key_exists($value, $this->_di->getRegistry()->sysmodules);

            if (!$result) {
                $validation->appendMessage(
                    new Validation\Message("Module with name '$value' doesn't exists.", $attribute)
                );
            }

            return $result;
        }

        return true;
    }
}