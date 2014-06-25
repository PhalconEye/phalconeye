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

use Engine\Behaviour\DIBehaviour;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Db\AbstractModel;
use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form\Behaviour\FieldSetBehaviour;
use Engine\Form\Behaviour\FormBehaviour;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Transaction\Exception;
use Phalcon\Mvc\View;
use Phalcon\Tag as Tag;
use Phalcon\Translate;
use Phalcon\Validation\Message\Group;

/**
 * Form class.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractForm implements ElementContainerInterface
{
    use FieldSetBehaviour,
        FormBehaviour;

    const
        /**
         * Request method type - delete.
         */
        METHOD_DELETE = 'DELETE',

        /**
         * Request method type - get.
         */
        METHOD_GET = 'GET',

        /**
         * Request method type - post.
         */
        METHOD_POST = 'POST',

        /**
         * Request method type - put.
         */
        METHOD_PUT = 'PUT';

    const
        /**
         * Encoding type - normal.
         */
        ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded',

        /**
         * Encoding type - with files and big data.
         */
        ENCTYPE_MULTIPART = 'multipart/form-data';

    const
        /**
         * Message about required field.
         */
        MESSAGE_FIELD_IS_REQUIRED = "Field '%s' is required!",

        /**
         * Message about empty field data.
         */
        MESSAGE_FIELD_IS_EMPTY = "Field '%s' can not be empty!",

        /**
         * Message about missing element.
         */
        MESSAGE_ELEMENT_NOT_FOUND = 'Element with name "%s" not found in form.',

        /**
         * Message about missing fieldset.
         */
        MESSAGE_FIELDSET_NOT_FOUND = 'Fieldset with name "%s" not found in form.',

        /**
         * Missing value in collection.
         */
        MESSAGE_VALUE_NOT_FOUND = 'Value "%s" not found in collection.';

    const
        /**
         * Fieldset content name.
         */
        FIELDSET_CONTENT = 'form_content',

        /**
         * Fieldset footer name.
         */
        FIELDSET_FOOTER = 'form_footer';

    const
        /**
         * Filter type 'string'.
         */
        FILTER_STRING = 'string',

        /**
         * Filter type 'email'.
         */
        FILTER_EMAIL = 'email',

        /**
         * Filter type 'int'.
         */
        FILTER_INT = 'int',

        /**
         * Filter type 'float'.
         */
        FILTER_FLOAT = 'float',

        /**
         * Filter type 'alphanum'.
         */
        FILTER_ALPHANUM = 'alphanum',

        /**
         * Filter type 'striptags'.
         */
        FILTER_STRIPTAGS = 'striptags',

        /**
         * Filter type 'trim'.
         */
        FILTER_TRIM = 'trim',

        /**
         * Filter type 'lower'.
         */
        FILTER_LOWER = 'lower',

        /**
         * Filter type 'upper'.
         */
        FILTER_UPPER = 'upper';

    /**
     * Form constructor.
     */
    public function __construct()
    {
        $this->__DIConstruct();

        // Collect profile info.
        $hasProfiler = $this->getDI()->has('profiler');
        if ($hasProfiler) {
            $this->getDI()->get('profiler')->start();
        }

        $this->_validation = new Validation($this);
        $this->_errors = new Group();
        $this->_notices = new Group();
        $this->_conditionsResolver = new ConditionResolver($this);

        // Set action.
        $pattern = '/' . str_replace('/', '\/', $this->getDI()->get('url')->getBaseUri()) . '/';
        $this->_action = preg_replace($pattern, '', $_SERVER['REQUEST_URI'], 1);

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }

        // Collect profile info.
        if ($hasProfiler) {
            $this->getDI()->get('profiler')->stop(get_called_class(), 'form');
        }
    }

    /**
     * Get layout view path.
     *
     * @return string
     */
    abstract public function getLayoutView();

    /**
     * Get element view path.
     *
     * @return string
     */
    abstract public function getElementView();

    /**
     * Get errors view path.
     *
     * @return string
     */
    abstract public function getErrorsView();

    /**
     * Get notices view path.
     *
     * @return string
     */
    abstract public function getNoticesView();

    /**
     * Get fieldset view path.
     *
     * @return string
     */
    abstract public function getFieldSetView();

    /**
     * Set form values.
     *
     * @param array                      $values    Form values.
     * @param AbstractForm|FieldSet|null $container Elements container.
     *
     * @return $this
     */
    public function setValues($values, $container = null)
    {
        if (empty($values)) {
            return $this;
        }

        if (!$container) {
            $container = $this;
        }

        /** @var AbstractElement|FieldSetBehaviour $element */
        foreach ($container->getAll() as $element) {
            $elementName = str_replace('[]', '', $element->getName());
            if ($element instanceof FieldSet) {
                $this->setValues($values, $element);
            } elseif (!$element->isIgnored()) {
                if (array_key_exists($elementName, $values)) {
                    $element->setValue($values[$elementName]);
                } elseif (array_key_exists($element->getName(), $values)) {
                    $element->setValue($values[$element->getName()]);
                }
            }
        }

        return $this;
    }

    /**
     * Set element value by name.
     *
     * @param string        $name      Element name.
     * @param string        $value     Element value.
     * @param Form|FieldSet $container Elements container.
     *
     * @throws Exception
     * @return $this
     */
    public function setValue($name, $value, $container = null)
    {
        $isRoot = false;
        $found = false;
        if (!$container) {
            $isRoot = true;
            $container = $this;
        }

        /** @var AbstractElement|FieldSetBehaviour $element */
        foreach ($container->getAll() as $element) {
            if ($element instanceof FieldSet) {
                $found = $found || $this->setValue($name, $value, $element);
            } elseif (!$element->isIgnored() && $element->getName() == $name) {
                $element->setValue($value);
                return $this;
            }
        }

        if ($isRoot && !$found) {
            throw new Exception(sprintf(self::MESSAGE_ELEMENT_NOT_FOUND, $name));
        }

        return $found;
    }

    /**
     * Get form values.
     *
     * @param Form|FieldSet|null $container Elements container.
     *
     * @return array
     */
    public function getValues($container = null)
    {
        if (!$container) {
            $container = $this;
        }

        /** @var AbstractElement|FieldSetBehaviour $element */
        $values = [];
        foreach ($container->getAll() as $element) {
            if ($element instanceof FieldSet) {
                $values += $this->getValues($element);
            } elseif (!$element->isIgnored()) {
                $values[str_replace('[]', '', $element->getName())] = $element->getValue();
            }
        }

        return $values;
    }

    /**
     * Get element value by name.
     *
     * @param string             $name      Element name.
     * @param Form|FieldSet|null $container Elements container.
     *
     * @throws Exception
     * @return mixed|null
     */
    public function getValue($name, $container = null)
    {
        $isRoot = false;
        $found = null;
        if (!$container) {
            $isRoot = true;
            $container = $this;
        }

        /** @var AbstractElement|FieldSetBehaviour $element */
        foreach ($container->getAll() as $element) {
            if ($element instanceof FieldSet) {
                $value = $this->getValue($name, $element);
                $found = (($value !== false) || $found ? $value : $found);
            } elseif (!$element->isIgnored() && $element->getName() == $name) {
                return $element->getValue();
            }
        }

        if ($isRoot && $found === false) {
            throw new Exception(sprintf(self::MESSAGE_ELEMENT_NOT_FOUND, $name));
        }

        return $found;
    }

    /**
     * Render form.
     *
     * @param string|null $layoutView Form view path.
     *
     * @return string
     */
    public function render($layoutView = null)
    {
        if (!$layoutView) {
            $layoutView = $this->getLayoutView();
        }

        /** @var View $view */
        $view = $this->getDI()->get('view');
        ob_start();
        $view->partial($layoutView, ['form' => $this]);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Form open tag.
     *
     * @return string
     */
    public function openTag()
    {
        return Tag::form(
            array_merge(
                $this->getAttributes(),
                [$this->getAction(), 'method' => $this->getMethod(), 'enctype' => $this->getEncodingType()]
            )
        );
    }

    /**
     * Form closing tag.
     *
     * @return string
     */
    public function closeTag()
    {
        return '</form>';
    }

    /**
     * Add footer fieldset.
     *
     * @return FieldSet
     */
    public function addContentFieldSet()
    {
        $fieldSet = new FieldSet(self::FIELDSET_CONTENT);
        $this->addFieldSet($fieldSet);

        return $fieldSet;
    }

    /**
     * Add footer fieldset.
     *
     * @param bool $combined Combine elements?
     *
     * @return FieldSet
     */
    public function addFooterFieldSet($combined = true)
    {
        $fieldSet = new FieldSet(self::FIELDSET_FOOTER, null, ['class' => self::FIELDSET_FOOTER]);
        $fieldSet->combineElements($combined);
        $this->addFieldSet($fieldSet);

        return $fieldSet;
    }


    /**
     * Validates the form.
     *
     * @param array $data               Data to validate.
     * @param bool  $skipEntityCreation Skip entity creation.
     *
     * @return boolean
     */
    public function isValid($data = null, $skipEntityCreation = false)
    {
        if (!$data) {
            $data = $this->getDI()->getRequest()->getPost();
        }

        // Check identity.
        $data = $this->parseDataByIdentity($data);

        /**
         * Check token.
         */
        if ($this->_useToken && !$this->getDI()->get('security')->checkToken()) {
            $this->addError('Token is not valid!');
            $this->setValues($data);
            return false;
        }

        /**
         * Check conditions.
         */
        $this->_conditionsResolver->resolve($data);
        foreach ($this->getFieldSets() as $fieldSet) {
            $this->_conditionsResolver->resolve($data, $fieldSet);
        }

        /**
         * Validate all elements and fieldsets.
         */
        $isValid = $this->_validateElements($this, $data, true);

        /**
         * Set filtered data again.
         */
        $this->setValues($data);

        /**
         * Get data from elements, it could change.
         */
        $data = $this->getValues();

        /**
         * There is something wrong...
         */
        if (!$isValid) {
            return false;
        }

        /**
         * Check validators.
         */
        $this->_checkValidators($data);

        if ($this->hasErrors()) {
            return false;
        }

        /**
         * ...and check entity.
         */
        return $this->_validateEntity($data, $skipEntityCreation);
    }

    /**
     * Check elements validators.
     *
     * @param array    $data      Form data.
     * @param FieldSet $container Form fieldset.
     *
     * @return array
     */
    protected function _checkValidators($data, $container = null)
    {
        if (!$container) {
            $container = $this;
        }

        $container->addErrorsGroup($container->getValidation()->validate($data));
        foreach ($container->getFieldSets() as $fieldSet) {
            $this->_checkValidators($data, $fieldSet);
        }
    }

    /**
     * Validate all elements in container.
     *
     * @param FieldSetBehaviour $container Elements container.
     * @param array             &$data     Form data.
     * @param bool              $isValid   Validation flag.
     *
     * @return bool
     */
    protected function _validateElements($container, &$data, $isValid)
    {
        // Check identity.
        $data = $this->parseDataByIdentity($data);

        /** @var AbstractElement|FieldSet $element */
        foreach ($container->getAll() as $element) {
            if ($element instanceof FieldSet) {
                $isValid = $isValid && $this->_validateElements($element, $data, $isValid);
            } else {
                $isValid = $isValid && $this->_validateElement($data, $element);
            }
        }

        return $isValid;
    }

    /**
     * Validate entity model.
     *
     * @param array           &$data   Data to validate.
     * @param AbstractElement $element Element object.
     *
     * @return bool
     */
    protected function _validateElement(&$data, $element)
    {
        $isValid = true;
        if ($element->isIgnored()) {
            return $isValid;
        }

        // Filter data.
        if (!empty($this->_filters[$element->getName()])) {
            foreach ($this->_filters[$element->getName()] as $filter) {
                $data[$element->getName()] = $this->getDI()->get('filter')->sanitize(
                    $data[$element->getName()],
                    $filter
                );
            }
        }

        // Check field requirement.
        if ($element->getOption('required')) {
            if (!isset($data[$element->getName()])) {
                $isValid = false;
                $this->addError(
                    sprintf($this->_(self::MESSAGE_FIELD_IS_REQUIRED), $this->_($element->getOption('label'))),
                    $element->getName()
                );
            }

            // Check that field can not be empty.
            if (!$element->getOption('emptyAllowed') && empty($data[$element->getName()])) {
                $isValid = false;
                $this->addError(
                    sprintf($this->_(self::MESSAGE_FIELD_IS_EMPTY), $this->_($element->getOption('label'))),
                    $element->getName()
                );
            }
        }

        /**
         * What data must have element, that was not sent to server? If used null as default - all will be null.
         */
        if ($this->_useDefaultValue) {
            if (!isset($data[$element->getName()]) || $data[$element->getName()] == '') {
                $data[$element->getName()] = $element->getOption('defaultValue');
            }
        }

        return $isValid;
    }

    /**
     * Validate entity model.
     *
     * @param array $data               Data to validate.
     * @param bool  $skipEntityCreation Skip entity creation.
     *
     * @throws \Exception
     * @return bool
     */
    protected function _validateEntity($data, $skipEntityCreation)
    {
        $isValid = true;
        if (!empty($this->_entities)) {

            // Create a transaction manager.
            $manager = $this->getDI()->getTransactions();

            // Request a transaction.
            $transaction = $manager->get();
            try {
                /** @var AbstractModel $entity */
                foreach ($this->_entities as $entity) {
                    if ($skipEntityCreation) {
                        $entity->assign($data);
                        if (method_exists($entity, 'validation')) {
                            $isValid = $entity->validation();
                        }
                    } else {
                        $isValid = $entity->save($data);
                    }
                }

                if (!$isValid) {
                    foreach ($entity->getMessages() as $message) {
                        $this->addError(
                            $this->_($message->getMessage()),
                            $message->getField()
                        );
                    }

                    $transaction->rollback('Failed to save model.');
                }

                // Everything goes fine, let's commit the transaction.
                $transaction->commit();
            } catch (\Exception $e) {
                if ($transaction->isValid()) {
                    $transaction->rollback();
                }
            }
        }

        return $isValid;
    }
}