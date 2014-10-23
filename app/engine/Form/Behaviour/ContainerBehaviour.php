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

namespace Engine\Form\Behaviour;

use Engine\Behaviour\DIBehaviour;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Form\AbstractElement;
use Engine\Form;
use Engine\Form\AbstractForm;

/**
 * Element container trait.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait ContainerBehaviour
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use ElementsBehaviour,
        TranslationBehaviour;

    /**
     * Form identity.
     *
     * @var string
     */
    protected $_identity = null;

    /**
     * Elements catalog.
     *
     * @var array
     */
    protected $_elements = [];

    /**
     * Elements order.
     *
     * @var array
     */
    protected $_order = [];

    /**
     * Current order.
     *
     * @var int
     */
    protected $_currentOrder = 0;

    /**
     * Used to set condition that one field required other.
     * Example: FieldA must be shown on form (or it's data processed)
     * only when in FieldB value is 'Type of FieldA is selected'.
     *
     * @var array
     */
    protected $_conditions = array();

    /**
     * Set form identity. In case you have two forms on page and identity isn't null (string required)
     * form validation method isValid will check current form post data and will not throw validation errors
     * if this form is not current by post data.
     *
     * @param string|null $identity Form identity name.
     *
     * @return $this
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    /**
     * Get current form identity.
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Parse data by container identity.
     *
     * @param array $data Array of data to check.
     *
     * @return array
     */
    public function parseDataByIdentity($data)
    {
        $id = $this->getIdentity();
        if ($id !== null) {
            if (!isset($data[$id]) || !is_array($data[$id])) {
                return false;
            }
            $data = $data[$id];
        }

        return $data;
    }

    /**
     * Add element to form.
     *
     * @param AbstractElement $element Element object.
     * @param int|null        $order   Element order.
     *
     * @return $this
     */
    public function add(AbstractElement $element, $order = null)
    {
        if (!$order) {
            $order = $this->_currentOrder++;
        }

        $element->setContainer($this);
        $this->_elements[$order] = $element;
        $this->_order[$element->getName()] = $order;

        if ($this->getIdentity() !== null) {
            $element->setAttribute('name', sprintf('%s[%s]', $this->getIdentity(), $element->getName()));
        }

        return $this;
    }

    /**
     * Get element by name.
     *
     * @param string $name Element name.
     *
     * @return AbstractElement
     */
    public function get($name)
    {
        $this->_checkElement($name);
        return $this->_elements[$this->_order[$name]];
    }

    /**
     * Check if element is exists.
     *
     * @param string $name Element name.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->_order[$name]);
    }

    /**
     * Remove element by name.
     *
     * @param string $name Element name.
     *
     * @return $this
     */
    public function remove($name)
    {
        $this->_checkElement($name);
        unset($this->_elements[$this->_order[$name]]);
        return $this;
    }

    /**
     * Set element order.
     * NOTICE: if you select existing order it will be overwritten.
     *
     * @param string $name               Element name.
     * @param int    $order              Element order.
     * @param bool   $updateCurrentOrder Update current form order.
     *
     * @return $this;
     */
    public function setOrder($name, $order, $updateCurrentOrder = false)
    {
        $this->_checkElement($name);

        $currentOrder = $this->_order[$name];
        $this->_elements[$order] = $this->get($name);
        $this->_order[$name] = $order;

        unset($this->_elements[$currentOrder]);

        if ($updateCurrentOrder) {
            $this->_currentOrder = $order + 1;
        }

        return $this;
    }

    /**
     * Get elements.
     *
     * @return AbstractElement[]
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Clear all elements from form.
     *
     * @return $this
     */
    public function clearElements()
    {
        $this->_elements = [];
        return $this;
    }

    /**
     * Check that element is in collection.
     *
     * @param string $name Element name.
     *
     * @throws Form\Exception
     * @return void
     */
    protected function _checkElement($name)
    {
        if (!$this->has($name)) {
            throw new Form\Exception(sprintf(AbstractForm::MESSAGE_ELEMENT_NOT_FOUND, $name));
        }
    }

    /**
     * Used to set condition that one field required other.
     * Example: FieldA must be shown on form (or it's data processed)
     * only when in FieldB value is 'Type of FieldA is selected'.
     *
     * @param mixed $fieldA     Field name.
     * @param mixed $fieldB     Field name that required by fieldA.
     * @param mixed $value      What value must have fieldB to validate fieldA.
     * @param mixed $comparison Comparison operator.
     * @param mixed $operator   Operator for value comparison.
     *
     * @return $this
     */
    public function setCondition(
        $fieldA,
        $fieldB,
        $value,
        $comparison = Form\ConditionResolver::COND_CMP_EQUAL,
        $operator = Form\ConditionResolver::COND_OP_AND
    )
    {
        $this->_conditions[$fieldA][] = array(
            'name' => $fieldB,
            'value' => $value,
            'comparison' => $comparison,
            'operator' => $operator
        );

        if ($this->get($fieldA)->getAttribute(Form\ConditionResolver::COND_FIELD_ATTRIBUTE)) {
            $this->get($fieldA)->setAttribute(
                Form\ConditionResolver::COND_FIELD_ATTRIBUTE,
                $this->get($fieldA)->getAttribute(Form\ConditionResolver::COND_FIELD_ATTRIBUTE) .
                ':' . $operator . ':' .
                $this->_getRelationString($fieldB, $value, $comparison)
            );
        } else {
            $this->get($fieldA)->setAttribute(
                Form\ConditionResolver::COND_FIELD_ATTRIBUTE,
                $this->_getRelationString($fieldB, $value, $comparison)
            );
        }
        return $this;
    }

    /**
     * Get related conditions.
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->_conditions;
    }

    /**
     * Set required element.
     *
     * @param string $name          Element name.
     * @param bool   $flag          If it required.
     * @param bool   $syncAttribute Sync attribute.
     *
     * @return Form
     */
    public function setRequired($name, $flag = true, $syncAttribute = true)
    {
        $element = $this->get($name);
        if ($syncAttribute) {
            $element->setAttribute('required', $flag ? 'required' : null);
        }

        $element->setOption('required', $flag);
        return $this;
    }

    /**
     * Set field as ignored.
     *
     * @param string $name Element name to ignore.
     * @param bool   $flag Is ignored?
     *
     * @return $this
     */
    public function setIgnored($name, $flag = true)
    {
        $this->get($name)->setOption('ignore', $flag);
        return $this;
    }

    /**
     * Get relation string.
     * This string will be stored in element attribute.
     *
     * @param string $field      Field name.
     * @param string $value      Field value.
     * @param string $comparison Comparison type.
     *
     * @return string
     */
    protected function _getRelationString($field, $value, $comparison)
    {
        return implode(':', array($field, $comparison, $value));
    }
}