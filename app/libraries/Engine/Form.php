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
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine;

use Phalcon\Tag as Tag;

class Form extends \Phalcon\Forms\Form
{

    /**
     * Method type constants
     */
    const METHOD_DELETE = 'delete';
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';


    /**
     * Encoding type constants
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART = 'multipart/form-data';

    /**
     * Form messages
     */
    const MESSAGE_FIELD_REQUIRED = "Field '%s' is required!";

    private $_trans = null;
    private $_elementsData = array();
    private $_orderedElements = array();
    private $_buttons = array();
    private $_currentOrder = 1;
    private $_errors = array();
    private $_notices = array();
    private $_useToken = false;
    private $_useNullValue = true;
    private $_validationFinished = false;
    private $_hasValidators = false;
    private $_elementsPrepared = false;
    private $_elementsOptions = array(
        'label',
        'description',
        'filter',
        'required',
        'validators'
    );


    private $_action = '';
    private $_title = '';
    private $_description = '';
    private $_attribs = array();
    private $_method = self::METHOD_POST;
    private $_enctype = self::ENCTYPE_URLENCODED;

    /**
     * Form constructor
     *
     * @param \Phalcon\Mvc\Model $entity
     */
    public function __construct(\Phalcon\Mvc\Model $entity = null)
    {
        $this->_trans = $this->di->get('trans');
        $this->_action = substr($_SERVER['REQUEST_URI'], 1);
        parent::__construct($entity);

        $this->init();
    }

    /**
     * Second initialization of the form
     */
    public function init()
    {
    }

    /**
     * Clear all elements from form
     */
    public function clearElements()
    {
        $this->_elementsData = array();
    }

    /**
     * Add new element to form
     *
     * @param $type - element type, see /Engine/Form/Element/*
     * @param $name - element name
     * @param array $params - element parameters
     * @param null $order - order of elemen t
     *
     * @return $this
     * @throws \Phalcon\Forms\Exception
     */
    public function addElement($type, $name, $params = array(), $order = null)
    {
        $elementClass = '\Engine\Form\Element\\' . ucfirst($type);
        if (!class_exists($elementClass))
            throw new \Phalcon\Forms\Exception("Element with type '{$type}' doesn't exist.");

        if ($order === null) {
            $order = $this->_currentOrder++;
        }

        // check file input
        if ($type == "file") {
            $this->_enctype = self::ENCTYPE_MULTIPART;
        }

        $onlyParams = array_intersect_key($params, array_flip($this->_elementsOptions));
        $attributes = array_diff_key($params, $onlyParams);

        /* @var \Phalcon\Forms\Element $element */
        $element = new $elementClass($name, $attributes);

        $this->_elementsData[$name] = array(
            'type' => $type,
            'element' => $element,
            'params' => $onlyParams,
            'attributes' => $attributes,
            'order' => $order
        );

        return $this;
    }

    /**
     * Clear all buttons from form
     */
    public function clearButtons()
    {
        $this->_buttons = array();
    }

    /**
     * Add button element
     *
     * @param $name
     * @param bool $isSubmit
     * @param array $params
     * @return $this
     */
    public function addButton($name, $isSubmit = false, $params = array())
    {
        $this->_buttons[$name] = array(
            'name' => $name,
            'is_submit' => $isSubmit,
            'params' => $params
        );

        return $this;
    }

    /**
     * Add link element
     *
     * @param $name
     * @param string $href
     * @param array $params
     * @return $this
     */
    public function addButtonLink($name, $href = 'javascript:;', $params = array())
    {
        $this->_buttons[$name] = array(
            'name' => $name,
            'href' => $href,
            'is_submit' => false,
            'is_link' => true,
            'params' => $params
        );

        return $this;
    }

    /**
     * Remove element by name (short function, same as removeElement)
     *
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        return $this->removeElement($name);
    }

    /**
     * Remove element by name
     *
     * @param $name
     * @return $this
     */
    public function removeElement($name)
    {
        if ($this->_elementsPrepared && $this->has($name))
            parent::remove($name);

        if (!empty($this->_elementsData[$name])) {
            unset($this->_elementsData[$name]);
        }
        return $this;
    }

    /**
     * Get element object
     *
     * @param $name
     * @return Form_Element
     * @throws \Phalcon\Forms\Exception
     */
    public function getElement($name)
    {
        if (empty($this->_elementsData[$name]))
            throw new \Phalcon\Forms\Exception('Form has no element "' . $name . '"');

        return $this->_elementsData[$name]['element'];
    }

    /**
     * Set element attribute
     *
     * @param $name
     * @param $key
     * @param $value
     * @return $this
     */
    public function setElementAttrib($name, $key, $value)
    {
        $element = $this->getElement($name);
        $element->setAttribute($key, $value);

        return $this;
    }

    /**
     * Set form option
     *
     * @param $key
     * @param $value
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
     * Set form attribute
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttrib($key, $value)
    {
        $this->_attribs[$key] = $value;
        return $this;
    }

    /**
     * Add error message
     *
     * @param $message
     * @return $this
     */
    public function addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    /**
     * Add notice message
     *
     * @param $message
     * @return $this
     */
    public function addNotice($message)
    {
        $this->_notices[] = $message;
        return $this;
    }

    /**
     * Set form values
     *
     * @param $values
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
     * Set element value by name
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setValue($name, $value)
    {
        $this->getElement($name)->setDefault($value);
        return $this;
    }

    /**
     * Get form values
     *
     * @return array
     */
    public function getValues()
    {
        if ($this->_entity !== null) {
            return $this->_entity;
        } else {
            $values = array();
            foreach ($this->_elementsData as $name => $element) {
                if (isset($_POST[$name])) {
                    $values[$name] = $this->_elementsData[$name]['attributes']['value'];
                } else {
                    $values[$name] = null;
                }
            }
            return $values;
        }
    }

    /**
     * Get element value by name
     *
     * @param string $name
     * @return mixed|null
     * @throws \Phalcon\Forms\Exception
     */
    public function getValue($name)
    {
        $name = str_replace('[]', '', $name);

        $value = parent::getValue($name);
        if ($value !== null)
            return $value;

        if (!isset($this->_elementsData[$name]))
            throw new \Phalcon\Forms\Exception('Form has no element "' . $name . '"');

        if (!isset($this->_elementsData[$name]['attributes']['value']))
            return null;

        return $this->_elementsData[$name]['attributes']['value'];
    }

    /**
     * Prepare elements to render or validation
     * This method add element and validator to Phalcon form class
     */
    protected function prepareElements()
    {
        if ($this->_elementsPrepared) return;

        $this->_orderedElements = $this->_elementsData;
        // sort elements by order
        usort($this->_orderedElements, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        // add elements to Phalcon form class
        foreach ($this->_orderedElements as $element) {
            if (!empty($element['params']['label']))
                $element['element']->setLabel($element['params']['label']);
            if (!empty($element['params']['description']))
                $element['element']->setDescription($element['params']['description']);
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
     * Validates the form
     *
     * @param array $data
     * @param object $entity
     * @return boolean
     */
    public function isValid($data = null, $entity = null)
    {
        if ($this->_useToken && !$this->di->get('security')->checkToken()) {
            $this->addError('Token is not valid!');
            return false;
        }

        $elementsData = $this->_elementsData;

        // save values in form
        $this->setValues($data);

        // Filter and check required
        $requiredFailed = false;
        $filter = new \Phalcon\Filter();
        foreach ($elementsData as $name => $element) {

            // filter
            if (isset($data[$name]) && isset($elementsData[$name]['params']['filter'])) {
                $data[$name] = $filter->sanitize($data[$name], $elementsData[$name]['params']['filter']);
            }

            if (isset($element['params']['required']) && $element['params']['required'] === true) {
                if (!isset($data[$name]) || empty($data[$name])) {
                    $this->addError(sprintf(self::MESSAGE_FIELD_REQUIRED, (!empty($element['params']['label'])) ? $this->_trans->_($element['params']['label']) : $name));
                    $requiredFailed = true;
                }
            }

            if ($this->_useNullValue) {
                if (isset($data[$name]) && empty($data[$name]))
                    $data[$name] = null;
            }
        }

        if ($requiredFailed)
            return false;

        $modelIsValid = true;
        $this->setValues($data);
        $this->prepareElements();
        if ($this->_entity !== null) {
            if ($entity !== null)
                $this->_entity = $entity;

            $this->bind($data, $this->_entity);
            $modelIsValid = $this->_entity->save();
        }
        $this->_validationFinished = true;
        return $modelIsValid && parent::isValid($data, $this->_entity);
    }

    /**
     * Renders form
     *
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function render($name = null, $attributes = null)
    {
        if (empty($this->_elementsData)) return "";
        $this->prepareElements();

        $content = Tag::form(array_merge($this->_attribs, array($this->_action, 'method' => $this->_method, 'enctype' => $this->_enctype))) . '<div>';

        ///////////////////////////////////////////
        /// Title and Description
        //////////////////////////////////////////
        if (!empty($this->_title) || !empty($this->_description)) {
            $content .= sprintf('<div class="form_header"><h3>%s</h3><p>%s</p></div>', $this->_trans->_($this->_title), $this->_trans->_($this->_description));
        }

        ///////////////////////////////////////////
        /// Error Messages
        //////////////////////////////////////////

        if (!empty($this->_errors) || count($this->_messages) != 0 || ($this->_entity != null && count($this->_entity->getMessages()) != 0)) {
            $content .= '<ul class="form_errors">';
            foreach ($this->_errors as $error) {
                $content .= sprintf('<li class="alert alert-error">%s</li>', $this->_trans->_($error));
            }
            if (count($this->_messages) != 0) {
                foreach ($this->getMessages() as $error) {
                    $content .= sprintf('<li class="alert alert-error">%s</li>', $this->_trans->_($error->getMessage()));
                }
            }
            if ($this->_entity != null) {
                foreach ($this->_entity->getMessages() as $error) {
                    $content .= sprintf('<li class="alert alert-error">%s</li>', $this->_trans->_($error));
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
                $content .= sprintf('<li class="alert alert-success">%s</li>', $this->_trans->_($notice));
            }
            $content .= '</ul>';
        }

        ///////////////////////////////////////////
        /// Elements
        //////////////////////////////////////////
        $content .= '<div class="form_elements">';
        $hiddenFields = array();
        /* @var \Phalcon\Forms\Element|Form_ElementInterface $element */
        foreach ($this as $element) {

            $elementData = $this->_elementsData[$element->getName()];

            if ($elementData['type'] == 'hidden') {
                $hiddenFields[] = $element;
                continue;
            }

            // multiple option specific
            if (isset($elementData['attributes']['multiple']))
                $element->setName($element->getName() . '[]');

            $content .= '<div>';
            $label = (!empty($elementData['params']['label']) ? sprintf('<label for="%s">%s</label>', $element->getName(), $this->_trans->_($elementData['params']['label'])) : '');
            $description = (!empty($elementData['params']['description']) ? sprintf('<p>%s</p>', $this->_trans->_($elementData['params']['description'])) : '');

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
            $tokenKey = $this->di->get('security')->getTokenKey();
            $token = $this->di->get('security')->getToken();
            $content .= sprintf('<input type="hidden" name="%s" value="%s">', $tokenKey, $token);
        }

        ///////////////////////////////////////////
        /// Buttons
        //////////////////////////////////////////
        if (!empty($this->_buttons)) {
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
                    $content .= sprintf('<a href="%s" %s>%s</a>', $this->di->get('url')->get($button['href']), $attribs, $this->_trans->_($button['name']));
                } else {
                    $content .= sprintf('<button%s%s>%s</button>', ($button['is_submit'] === true ? ' type="submit"' : ''), $attribs, $this->_trans->_($button['name']));
                }

            }
            $content .= '</div>';
        }

        $content .= '</div>' . Tag::endForm();


        return $content;
    }
}