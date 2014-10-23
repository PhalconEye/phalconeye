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
use Phalcon\Validation\Validator\Regex as PhalconRegexValidator;
use Phalcon\Validation\ValidatorInterface;

/**
 * Form validator - Regex.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Validator
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Regex extends PhalconRegexValidator implements ValidatorInterface
{
    /**
     * Create regex validator.
     *
     * @param array $params Validator parameters.
     */
    public function __construct($params = [])
    {
        if (isset($params['message'])) {
            $params['message'] = DI::getDefault()->get('i18n')->_($params['message']);
        }

        parent::__construct($params);
    }
}