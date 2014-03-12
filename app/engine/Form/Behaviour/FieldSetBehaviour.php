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

use Engine\Form;
use Engine\Form\AbstractForm;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;

/**
 * FieldSet behaviour.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait FieldSetBehaviour
{
    use ContainerBehaviour;

    /**
     * Fieldsets.
     *
     * @var array
     */
    protected $_fieldSets = [];

    /**
     * Validation object.
     *
     * @var Form\Validation
     */
    protected $_validation;

    /**
     * Current errors.
     *
     * @var Group
     */
    protected $_errors;

    /**
     * Current notices.
     *
     * @var Group
     */
    protected $_notices;

    /**
     * Conditions resolver.
     *
     * @var Form\ConditionResolver
     */
    protected $_conditionsResolver;

    /**
     * Add fieldset.
     *
     * @param Form\FieldSet $fieldSet FieldSet object.
     * @param int|null      $order    FieldSet order.
     *
     * @return $this
     */
    public function addFieldSet(Form\FieldSet $fieldSet, $order = null)
    {
        if (!$order) {
            $order = $this->_currentOrder++;
        }

        $this->_fieldSets[$order] = $fieldSet;
        $this->_order[$fieldSet->getName()] = $order;

        return $this;
    }

    /**
     * Get fieldset.
     *
     * @param string $name FieldSet name.
     *
     * @return Form\FieldSet
     */
    public function getFieldSet($name)
    {
        $this->_checkFieldSet($name);
        return $this->_fieldSets[$this->_order[$name]];
    }

    /**
     * Check if fieldset is exists.
     *
     * @param string $name Fieldset name.
     *
     * @return bool
     */
    public function hasFieldsSet($name)
    {
        return isset($this->_order[$name]);
    }

    /**
     * Remove fieldset.
     *
     * @param string $name FieldSet name.
     *
     * @return $this
     */
    public function removeFieldSet($name)
    {
        $this->_checkFieldSet($name);
        unset($this->_fieldSets[$name]);
        return $this;
    }

    /**
     * Get all fieldsets.
     *
     * @return array
     */
    public function getFieldSets()
    {
        return $this->_fieldSets;
    }

    /**
     * Get all elements and fieldsets from form.
     *
     * @return array
     */
    public function getAll()
    {
        $items = $this->getElements() + $this->getFieldSets();
        ksort($items);
        return $items;
    }

    /**
     * Used to set condition that one field required other
     * Example: Fieldset must be shown on form (or it's data processed)
     * only when in Fieldset value is 'Type of Fieldset is selected'.
     *
     * @param mixed $fieldSet   Fieldset name.
     * @param mixed $field      Field name that required fieldA.
     * @param mixed $value      What value must have fieldB to validate fieldA.
     * @param mixed $comparison Comparison operator.
     * @param mixed $operator   Operator for value comparison.
     *
     * @return $this
     */
    public function setFieldSetCondition(
        $fieldSet,
        $field,
        $value,
        $comparison = Form\ConditionResolver::COND_CMP_EQUAL,
        $operator = Form\ConditionResolver::COND_OP_AND
    )
    {
        $this->_conditions[$fieldSet][] = array(
            'isFieldSet' => true,
            'name' => $field,
            'value' => $value,
            'comparison' => $comparison,
            'operator' => $operator
        );

        if ($this->getFieldSet($fieldSet)->getAttribute(Form\ConditionResolver::COND_FIELD_ATTRIBUTE)) {
            $this->getFieldSet($fieldSet)->setAttribute(
                Form\ConditionResolver::COND_FIELD_ATTRIBUTE,
                $this->getFieldSet($fieldSet)->getAttribute(Form\ConditionResolver::COND_FIELD_ATTRIBUTE) .
                ':' . $operator . ':' .
                $this->_getRelationString($field, $value, $comparison)
            );
        } else {
            $this->getFieldSet($fieldSet)->setAttribute(
                Form\ConditionResolver::COND_FIELD_ATTRIBUTE,
                $this->_getRelationString($field, $value, $comparison)
            );
        }

        return $this;
    }

    /**
     * Get validation object.
     *
     * @return Form\Validation
     */
    public function getValidation()
    {
        return $this->_validation;
    }

    /**
     * Add error message.
     *
     * @param Message|string $message Message text.
     * @param string|null    $field   Field name.
     *
     * @return $this
     */
    public function addError($message, $field = null)
    {
        if (!$message instanceof Message) {
            if (is_object($message)) {
                $message = $message->__toString();
            }
            $message = new Message($this->_($message), $field);
        }

        $this->_errors->appendMessage($message);

        return $this;
    }

    /**
     * Add errors as group.
     *
     * @param Group $group Group object.
     *
     * @return $this
     */
    public function addErrorsGroup(Group $group)
    {
        $messages = iterator_to_array($group);
        /** @var Message $message */
        foreach ($messages as $message) {
            $message->setMessage($this->_($message->getMessage()));
            $this->_errors->appendMessage($message);
        }
        return $this;
    }

    /**
     * Add notice message.
     *
     * @param Message|string $message Message text.
     * @param string|null    $field   Field name.
     *
     * @return $this
     */
    public function addNotice($message, $field = null)
    {
        if (!$message instanceof Message) {
            if (is_object($message)) {
                $message = $message->__toString();
            }
            $message = new Message($this->_($message), $field);
        }

        $this->_notices->appendMessage($message);

        return $this;
    }

    /**
     * Get form errors.
     *
     * @param string|null $field Field name
     *
     * @return array
     */
    public function getErrors($field = null)
    {
        if ($field) {
            $errors = $this->_errors->filter($field);
        } else {
            $errors = iterator_to_array($this->_errors);
        }

        foreach ($this->getFieldSets() as $fieldSet) {
            $errors += $fieldSet->getErrors($field);
        }

        return $errors;
    }

    /**
     * Check if form has errors.
     *
     * @param string|null $field Field name.
     *
     * @return bool
     */
    public function hasErrors($field = null)
    {
        $errors = $this->getErrors($field);
        return !empty($errors);
    }

    /**
     * Get form notices.
     *
     * @param string|null $field Field name
     *
     * @return array
     */
    public function getNotices($field = null)
    {
        if ($field) {
            $notices = $this->_notices->filter($field);
        } else {
            $notices = iterator_to_array($this->_notices);
        }

        foreach ($this->getFieldSets() as $fieldSet) {
            $notices += $fieldSet->getNotices($field);
        }

        return $notices;
    }

    /**
     * Check if form has errors.
     *
     * @param string|null $field Field name.
     *
     * @return bool
     */
    public function hasNotices($field = null)
    {
        $notices = $this->getNotices($field);
        return !empty($notices);
    }

    /**
     * Check that fieldset is in collection.
     *
     * @param string $name Fieldset name.
     *
     * @throws Form\Exception
     * @return void
     */
    protected function _checkFieldSet($name)
    {
        if (!$this->has($name)) {
            throw new Form\Exception(sprintf(AbstractForm::MESSAGE_FIELDSET_NOT_FOUND, $name));
        }
    }
}