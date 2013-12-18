<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
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

namespace Engine;

use Engine\Db\AbstractModel;
use Engine\Form\Element;
use Phalcon\Filter;
use Phalcon\Forms\Form as PhalconForm;
use Phalcon\Tag as Tag;
use Phalcon\Translate;

/**
 * Form class.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Form extends PhalconForm
{

    const
        /**
         * Request method type - delete.
         */
        METHOD_DELETE = 'delete',

        /**
         * Request method type - get.
         */
        METHOD_GET = 'get',

        /**
         * Request method type - post.
         */
        METHOD_POST = 'post',

        /**
         * Request method type - put.
         */
        METHOD_PUT = 'put';


    const
        /**
         * Encoding type - normal.
         */
        ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded',

        /**
         * Encoding type - with files and big data.
         */
        ENCTYPE_MULTIPART = 'multipart/form-data';

    /**
     * Form messages
     */
    const MESSAGE_FIELD_REQUIRED = "Field '%s' is required!";

    /**
     * Translation object.
     *
     * @var Translate
     */
    private $_trans = null;

    /**
     * Element related data.
     *
     * @var array
     */
    private $_elementsData = [];

    /**
     * Already ordered elements.
     *
     * @var array
     */
    private $_orderedElements = [];

    /**
     * Form buttons.
     *
     * @var array
     */
    private $_buttons = [];

    /**
     * Current order index.
     *
     * @var int
     */
    private $_currentOrder = 1;

    /**
     * Current errors.
     *
     * @var array
     */
    private $_errors = [];

    /**
     * Current notices.
     *
     * @var array
     */
    private $_notices = [];

    /**
     * Use token?
     *
     * @var bool
     */
    private $_useToken = false;

    /**
     * Use null values if element is empty.
     *
     * @var bool
     */
    private $_useNullValue = true;

    /**
     * Is validation finished?
     *
     * @var bool
     */
    private $_validationFinished = false;

    /**
     * Form has validators?
     *
     * @var bool
     */
    private $_hasValidators = false;

    /**
     * Elements has been prepared?
     *
     * @var bool
     */
    private $_elementsPrepared = false;

    /**
     * All elements options.
     *
     * @var array
     */
    private $_elementsOptions = [
        'label',
        'description',
        'filters',
        'required',
        'validators'
    ];

    /**
     * Current action.
     *
     * @var string
     */
    protected $_action;

    /**
     * Form title.
     *
     * @var string
     */
    private $_title;

    /**
     * From description.
     *
     * @var string
     */
    private $_description;

    /**
     * Form attributes.
     *
     * @var array
     */
    private $_attribs = [];

    /**
     * Form current method.
     *
     * @var string
     */
    private $_method = self::METHOD_POST;

    /**
     * Form current encyption type.
     *
     * @var string
     */
    private $_enctype = self::ENCTYPE_URLENCODED;

    /**
     * Form constructor.
     *
     * @param AbstractModel $entity Some entity.
     */
    public function __construct(AbstractModel $entity = null)
    {
        // Collect profile info.
        $config = $this->di->get('config');
        if ($config->application->debug && $this->di->has('profiler')) {
            $this->di->get('profiler')->start();
        }

        $this->_trans = $this->di->get('trans');
        $this->_action = substr($_SERVER['REQUEST_URI'], 1);
        parent::__construct($entity);

        if (method_exists($this, 'init')) {
            $this->init();
        }

        // Collect profile info.
        if ($config->application->debug && $this->di->has('profiler')) {
            $this->di->get('profiler')->stop(get_called_class(), 'form');
        }
    }

    /**
     * Clear all elements from form.
     *
     * @return void
     */
    public function clearElements()
    {
        $this->_elementsData = [];
    }


    /**
     * Clear all buttons from form.
     *
     * @return void
     */
    public function clearButtons()
    {
        $this->_buttons = [];
    }

    /**
     * Add new element to form.
     *
     * @param string $type   Element type, look at \Engine\Form\Element\*
     * @param string $name   Element name
     * @param array  $params Element params.
     * @param null   $order  Element order.
     *
     * @throws Exception
     * @return $this
     */
    public function addElement($type, $name, $params = [], $order = null)
    {
        $elementClass = '\Engine\Form\Element\\' . ucfirst($type);
        if (!class_exists($elementClass)) {
            throw new Exception("Element with type '{$type}' doesn't exist.");
        }

        if ($order === null) {
            $order = $this->_currentOrder++;
        }

        // Check file input.
        if ($type == "file") {
            $this->_enctype = self::ENCTYPE_MULTIPART;
        }

        $onlyParams = array_intersect_key($params, array_flip($this->_elementsOptions));
        $attributes = array_diff_key($params, $onlyParams);

        /* @var Element $element */
        $element = new $elementClass($name, $attributes);

        // Set default value.
        if ($this->_entity && isset($this->_entity->$name)) {
            $element->setDefault($this->_entity->$name);
        }

        $this->_elementsData[$name] = [
            'type' => $type,
            'element' => $element,
            'params' => $onlyParams,
            'attributes' => $attributes,
            'order' => $order
        ];

        return $this;
    }

    /**
     * Add button element.
     *
     * @param string $name     Button name.
     * @param bool   $isSubmit Button type is submit ?
     * @param array  $params   Button param.
     *
     * @return $this
     */
    public function addButton($name, $isSubmit = false, $params = [])
    {
        $this->_buttons[$name] = [
            'name' => $name,
            'is_submit' => $isSubmit,
            'params' => $params
        ];

        return $this;
    }

    /**
     * Add link element.
     *
     * @param string $name   Link name.
     * @param string $href   Link href.
     * @param array  $params Link params.
     *
     * @return $this
     */
    public function addButtonLink($name, $href = 'javascript:;', $params = [])
    {
        $this->_buttons[$name] = [
            'name' => $name,
            'href' => $href,
            'is_submit' => false,
            'is_link' => true,
            'params' => $params
        ];

        return $this;
    }

    /**
     * Remove element by name (short function, same as removeElement).
     *
     * @param string $name Element name.
     *
     * @return $this
     */
    public function remove($name)
    {
        return $this->removeElement($name);
    }

    /**
     * Remove element by name.
     *
     * @param string $name Element name.
     *
     * @return $this
     */
    public function removeElement($name)
    {
        if ($this->_elementsPrepared && $this->has($name)) {
            parent::remove($name);
        }

        if (!empty($this->_elementsData[$name])) {
            unset($this->_elementsData[$name]);
        }

        return $this;
    }

    /**
     * Get element object.
     *
     * @param string $name Element name.
     *
     * @return Element
     * @throws Exception
     */
    public function getElement($name)
    {
        if (empty($this->_elementsData[$name])) {
            throw new Exception('Form has no element "' . $name . '"');
        }

        return $this->_elementsData[$name]['element'];
    }

    /**
     * Set element attribute.
     *
     * @param string $name  Element name.
     * @param string $key   Attribute name.
     * @param string $value Attribute value.
     *
     * @return $this
     */
    public function setElementAttrib($name, $key, $value)
    {
        $element = $this->getElement($name);
        $element->setAttribute($key, $value);

        return $this;
    }

    /**
     * Set form option.
     *
     * @param string $key   Option name.
     * @param string $value Option value.
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        if (property_exists($this, "_" . $key)) {
            $this->{"_" . $key} = $value;
        }

        return $this;
    }

    /**
     * Set form attribute.
     *
     * @param string $key   Attribute name.
     * @param string $value Attribute value.
     *
     * @return $this
     */
    public function setAttrib($key, $value)
    {
        $this->_attribs[$key] = $value;

        return $this;
    }

    /**
     * Add error message.
     *
     * @param string $message Message text.
     *
     * @return $this
     */
    public function addError($message)
    {
        $this->_errors[] = $message;

        return $this;
    }

    /**
     * Add notice message.
     *
     * @param string $message Message text.
     *
     * @return $this
     */
    public function addNotice($message)
    {
        $this->_notices[] = $message;

        return $this;
    }

    /**
     * Set form values.
     *
     * @param array $values Form values.F
     *
     * @return $this
     */
    public function setValues($values)
    {
        foreach ($this->_elementsData as $name => $element) {
            if (isset($values[$name])) {
                $element['element']->setDefault($values[$name]);
                $this->_elementsData[$name]['attributes']['value'] = $values[$name];
            } else {
                $element['element']->setDefault(null);
            }
        }

        return $this;
    }

    /**
     * Set element value by name.
     *
     * @param string $name  Element name.
     * @param string $value Element value.
     *
     * @return $this
     */
    public function setValue($name, $value)
    {
        $this->getElement($name)->setDefault($value);

        return $this;
    }

    /**
     * Get form values.
     *
     * @param bool $getEntity Get entity if possible instead of array data.
     *
     * @return array
     */
    public function getValues($getEntity = true)
    {
        if ($getEntity && $this->_entity !== null) {
            return $this->_entity;
        } else {
            $values = [];
            foreach (array_keys($this->_elementsData) as $name) {
                if (isset($_POST[$name]) && isset($this->_elementsData[$name]['attributes']['value'])) {
                    $values[$name] = $this->_elementsData[$name]['attributes']['value'];
                } else {
                    $values[$name] = null;
                }
            }

            return $values;
        }
    }

    /**
     * Get element value by name.
     *
     * @param string $name Element name.
     *
     * @throws Exception
     * @return mixed|null
     */
    public function getValue($name)
    {
        $name = str_replace('[]', '', $name);

        $value = parent::getValue($name);
        if ($value !== null) {
            return $value;
        }

        if (!isset($this->_elementsData[$name])) {
            throw new Exception('Form has no element "' . $name . '"');
        }

        if (!isset($this->_elementsData[$name]['attributes']['value'])) {
            return null;
        }

        return $this->_elementsData[$name]['attributes']['value'];
    }

    /**
     * Prepare elements to render or validation.
     * This method add element and validator to Phalcon form class.
     *
     * @return void
     */
    protected function prepareElements()
    {
        if ($this->_elementsPrepared) {
            return;
        }

        $this->_orderedElements = $this->_elementsData;

        // Sort elements by order.
        usort(
            $this->_orderedElements, function ($a, $b) {
                return $a['order'] - $b['order'];
            }
        );

        // Add elements to Phalcon form class.
        foreach ($this->_orderedElements as $element) {
            if (!empty($element['params']['label'])) {
                $element['element']->setLabel($element['params']['label']);
            }
            if (!empty($element['params']['description'])) {
                $element['element']->setDescription($element['params']['description']);
            }
            if (!empty($element['params']['filters'])) {
                $element['element']->setFilters($element['params']['filters']);
            }
            if (!empty($element['params']['validators']) && is_array($element['params']['validators'])) {
                $this->_hasValidators = true;
                foreach ($element['params']['validators'] as $validator) {
                    $element['element']->addValidator($validator);
                }
            }

            $this->add($element['element']);
        }

        $this->_elementsPrepared = true;
    }

    /**
     * Validates the form.
     *
     * @param array         $data               Data to validate.
     * @param AbstractModel $entity             Entity to validate.
     * @param bool          $skipEntityCreation Skip entity creation.
     *
     * @return boolean
     */
    public function isValid($data = null, $entity = null, $skipEntityCreation = false)
    {
        if ($this->_useToken && !$this->di->get('security')->checkToken()) {
            $this->addError('Token is not valid!');

            return false;
        }
        $elementsData = $this->_elementsData;

        // Save values in form.
        $this->setValues($data);

        // Filter and check required.
        $requiredFailed = false;
        $filter = new Filter();
        foreach ($elementsData as $name => $element) {

            // filter
            if (isset($data[$name]) && isset($elementsData[$name]['params']['filter'])) {
                $data[$name] = $filter->sanitize($data[$name], $elementsData[$name]['params']['filter']);
            }

            if (isset($element['params']['required']) && $element['params']['required'] === true) {
                if (!isset($data[$name]) || empty($data[$name])) {
                    $this->addError(
                        sprintf(
                            self::MESSAGE_FIELD_REQUIRED,
                            !empty($element['params']['label']) ? $this->_trans->_($element['params']['label']) : $name
                        )
                    );
                    $requiredFailed = true;
                }
            }

            if ($this->_useNullValue) {
                if (isset($data[$name]) && empty($data[$name])) {
                    $data[$name] = null;
                }
            }
        }

        if ($requiredFailed) {
            return false;
        }

        $this->setValues($data);
        $this->prepareElements();

        $parentIsValid = parent::isValid($data, $this->_entity);
        $this->_validationFinished = true;

        return $this->_isEntityValid($entity, $data, $parentIsValid, $skipEntityCreation);
    }

    protected function _isEntityValid($entity, $data, $parentIsValid, $skipEntityCreation)
    {
        $modelIsValid = true;
        if ($this->_entity !== null) {
            if ($entity !== null) {
                $this->_entity = $entity;
            }

            $this->bind($data, $this->_entity);
            if ($parentIsValid) {
                if ($skipEntityCreation) {
                    if (method_exists($this->_entity, 'validation')) {
                        $modelIsValid = ($this->_entity->validation() !== false);
                    }
                } else {
                    $modelIsValid = $this->_entity->save();
                }
            }
        }

        return $modelIsValid && $parentIsValid;
    }

    /**
     * Render the form.
     *
     * @return string
     *
     * @TODO: Refactor this.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render()
    {
        if (empty($this->_elementsData)) {
            return "";
        }
        $trans = $this->_trans;
        $this->prepareElements();

        $content =
            Tag::form(
                array_merge(
                    $this->_attribs,
                    [$this->_action, 'method' => $this->_method, 'enctype' => $this->_enctype]
                )
            ) . '<div>';

        ///////////////////////////////////////////
        /// Title and Description
        //////////////////////////////////////////
        if (!empty($this->_title) || !empty($this->_description)) {
            $content .= '<div class="form_header"><h3>' .
                $trans->_($this->_title) . '</h3><p>' .
                $trans->_($this->_description) . '</p></div>';
        }

        ///////////////////////////////////////////
        /// Error Messages
        //////////////////////////////////////////

        if (
            !empty($this->_errors) ||
            count($this->_messages) != 0 ||
            ($this->_entity != null && count($this->_entity->getMessages()) != 0)
        ) {
            $content .= '<ul class="form_errors">';
            foreach ($this->_errors as $error) {
                $content .= sprintf('<li class="alert alert-error">%s</li>', $trans->_($error));
            }
            if (count($this->_messages) != 0) {
                foreach ($this->getMessages() as $error) {
                    $content .= sprintf('<li class="alert alert-error">%s</li>', $trans->_($error->getMessage()));
                }
            }
            if ($this->_entity != null && $this->_entity->getMessages()) {
                foreach ($this->_entity->getMessages() as $error) {
                    $content .= sprintf('<li class="alert alert-error">%s</li>', $trans->_($error));
                }
            }
            $content .= '</ul>';
        }

        ///////////////////////////////////////////
        /// Notice Messages
        //////////////////////////////////////////
        if (!empty($this->_notices)) {
            $content .= '<ul class="form_notices">';
            foreach ($this->_notices as $notice) {
                $content .= sprintf('<li class="alert alert-success">%s</li>', $trans->_($notice));
            }
            $content .= '</ul>';
        }

        ///////////////////////////////////////////
        /// Elements
        //////////////////////////////////////////

        $content .= '<div class="form_elements">';
        $hiddenFields = [];
        /* @var Element $element */
        foreach ($this as $element) {

            $elementData = $this->_elementsData[$element->getName()];

            if ($elementData['type'] == 'hidden') {
                $hiddenFields[] = $element;
                continue;
            }

            // multiple option specific
            if (isset($elementData['attributes']['multiple'])) {
                $element->setName($element->getName() . '[]');
            }

            $content .= '<div class="form_element_container">';
            $label = (!empty($elementData['params']['label']) ?
                sprintf('<label for="%s">%s</label>', $element->getName(), $trans->_($elementData['params']['label'])) :
                ''
            );
            $description = (!empty($elementData['params']['description']) ?
                sprintf('<p>%s</p>', $trans->_($elementData['params']['description'])) :
                ''
            );

            if ($element->useDefaultLayout()) {
                $content .= sprintf('<div class="form_label">%s%s</div>', $label, $description);
                $content .= sprintf('<div class="form_element">%s</div>', $element->render());
            } else {
                $content .= $element->render();
            }

            $content .= '</div>';
        }
        $content .= '</div><div class="clear"></div>';

        // render hidden fields
        foreach ($hiddenFields as $element) {
            $content .= $element->render();
        }

        ///////////////////////////////////////////
        /// Token
        //////////////////////////////////////////
        if ($this->_useToken) {
            $tokenKey = $this->security->getTokenKey();
            $token = $this->security->getToken();
            $content .= sprintf('<input type="hidden" name="%s" value="%s">', $tokenKey, $token);
        }

        ///////////////////////////////////////////
        /// Buttons
        //////////////////////////////////////////
        if (!empty($this->_buttons)) {
            $url = $this->di->get('url');
            $content .= '<div class="form_footer">';
            foreach ($this->_buttons as $button) {

                $attribs = "";
                if (!empty($button['params']['class'])) {
                    $button['params']['class'] .= ' btn';
                } else {
                    $button['params']['class'] = 'btn';
                }

                if ($button['is_submit'] === true) {
                    $button['params']['class'] .= ' btn-primary';
                }

                foreach ($button['params'] as $key => $param) {
                    $attribs .= ' ' . $key . '="' . $param . '"';
                }

                if (!empty($button['is_link']) && $button['is_link'] == true) {
                    $content .= sprintf(
                        '<a href="%s" %s>%s</a>', $url->get($button['href']), $attribs, $trans->_($button['name'])
                    );
                } else {
                    $content .= sprintf(
                        '<button%s%s>%s</button>',
                        ($button['is_submit'] === true ? ' type="submit"' : ''),
                        $attribs,
                        $this->_trans->_($button['name'])
                    );
                }

            }
            $content .= '</div>';
        }
        $content .= '</div></form>';

        return $content;
    }
}