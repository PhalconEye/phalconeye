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

namespace Engine\Form;

use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form;
use Engine\Form\Behaviour\FormBehaviour;
use Engine\Behaviour\TranslationBehaviour;
use Phalcon\DI;
use Phalcon\Filter;
use Phalcon\Tag as Tag;
use Phalcon\Translate;

/**
 * ConditionResolver.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ConditionResolver
{
    const
        /**
         * Value is equal.
         */
        COND_CMP_EQUAL = '==',

        /**
         * Value isn't equal.
         */
        COND_CMP_NOT_EQUAL = '!=',

        /**
         * Value is equal.
         */
        COND_CMP_GREATER = '>',

        /**
         * Value isn't equal.
         */
        COND_CMP_LESS = '<',

        /**
         * Value is equal.
         */
        COND_CMP_GREATER_OR_EQUAL = '>=',

        /**
         * Value isn't equal.
         */
        COND_CMP_LESS_OR_EQUAL = '<=',

        /**
         * Value is equal.
         */
        COND_OP_AND = 'and',

        /**
         * Value isn't equal.
         */
        COND_OP_OR = 'or',

        /**
         * Name of element attribute that will handle relation info.
         */
        COND_FIELD_ATTRIBUTE = 'data-related';

    /**
     * Container object.
     *
     * @var Form|FieldSet
     */
    protected $_container;

    /**
     * Create condition resolver.
     *
     * @param Form|FieldSet $container Container object.
     */
    public function __construct($container)
    {
        $this->_container = $container;
    }

    /**
     * Check elements conditions.
     *
     * @param array    $data      Form data.
     * @param FieldSet $container Form fieldset.
     *
     * @return void
     */
    public function resolve($data, $container = null)
    {
        if (!$container) {
            $container = $this->_container;
        }

        $conditions = $container->getConditions();
        foreach ($conditions as $fieldA => $condition) {
            $allowed = null;
            $isFieldSet = false;
            foreach ($condition as $fieldB) {
                if ($allowed === null) {
                    $allowed = array_key_exists($fieldB['name'], $data) &&
                        $this->getComparison(
                            $fieldB['comparison'],
                            $data[$fieldB['name']],
                            $fieldB['value']
                        );
                } else {
                    $allowed =
                        $this->getOperator(
                            $fieldB['operator'],
                            $allowed,
                            array_key_exists($fieldB['name'], $data) &&
                            $this->getComparison(
                                $fieldB['comparison'],
                                $data[$fieldB['name']],
                                $fieldB['value']
                            )
                        );
                }

                $isFieldSet = $isFieldSet || !empty($fieldB['isFieldSet']);
            }

            if ($isFieldSet) {
                $tempFieldSet = $container->getFieldSet($fieldA);
                $this->_setIgnored($tempFieldSet, !$allowed);
                if (!$allowed) {
                    $tempFieldSet->getValidation()->clearValidators();
                }
            } else {
                $container->setIgnored($fieldA, !$allowed);
                if (!$allowed) {
                    $container->getValidation()->remove($fieldA);
                }
            }
        }
    }

    /**
     * Compare.
     *
     * @param string $cmp Comparison operator.
     * @param mixed  $x   X value.
     * @param mixed  $y   Y value.
     *
     * @return bool
     */
    public function getComparison($cmp, $x, $y)
    {
        switch ($cmp) {
            case self::COND_CMP_EQUAL:
                return $x == $y;
            case self::COND_CMP_GREATER:
                return $x > $y;
            case self::COND_CMP_GREATER_OR_EQUAL:
                return $x >= $y;
            case self::COND_CMP_LESS:
                return $x < $y;
            case self::COND_CMP_LESS_OR_EQUAL:
                return $x <= $y;
            case self::COND_CMP_NOT_EQUAL:
                return $x != $y;
        }

        return true;
    }

    /**
     * Get operator.
     *
     * @param string $op Operator.
     * @param mixed  $x  X value.
     * @param mixed  $y  Y value.
     *
     * @return bool
     */
    public function getOperator($op, $x, $y)
    {
        switch ($op) {
            case self::COND_OP_AND:
                return $x && $y;
            case self::COND_OP_OR:
                return $x || $y;
        }

        return true;
    }

    /**
     * Set ignored option.
     *
     * @param Form|FieldSet $container Container object.
     * @param bool          $flag      Ignore flag.
     *
     * @return void
     */
    protected function _setIgnored($container, $flag)
    {
        foreach ($container->getAll() as $element) {
            if ($element instanceof FieldSet) {
                $this->_setIgnored($element, $flag);
            } else {
                $element->setOption('ignore', $flag);
            }
        }
    }
}