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

use Engine\Behavior\DIBehavior;
use Engine\Package\PackageData;
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
     * @param DIBehavior|Di $di Dependency injection.
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
            $result = $this->_exists($value);

            if (!$result) {
                $validation->appendMessage(
                    new Validation\Message("Module with name '$value' doesn't exists.", $attribute)
                );
            }

            return $result;
        }

        return true;
    }

    /**
     * Check module exists in modules.
     *
     * @param string $module Module name.
     *
     * @return bool Check result.
     */
    protected function _exists($module)
    {
        foreach ($this->_di->getModules()->getPackages() as $moduleData) {
            if (!$moduleData->isMetadata(PackageData::METADATA_IS_SYSTEM) && $moduleData->getName() == $module) {
                return true;
            }
        }

        return false;
    }
}