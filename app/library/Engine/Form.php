<?php

use Phalcon\Tag as Tag;

class Form
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

    /**
     * Ignored fields by default
     *
     * @var array
     */
    private $_ignoreFields = array('id', 'creation_date', 'modified_date');

    /**
     * Ignored field types by default
     *
     * @var array
     */
    private $_ignoreTypes = array('submitButton');

    /**
     * Required fields
     *
     * @var array
     */
    private $_requiredFields = array();

    public $di = null;

    private $_trans = null;
    private $_elements = array();
    private $_buttons = array();
    private $_model = null;
    private $_data = array();
    private $_currentOrder = 1;
    private $_errors = array();
    private $_notices = array();
    private $_useToken = true;


    private $_action = '';
    private $_title = '';
    private $_description = '';
    private $_attribs = array();
    private $_method = self::METHOD_POST;
    private $_enctype = self::ENCTYPE_URLENCODED;

    /**
     * @param \Phalcon\Mvc\Model|null $model
     */
    public function __construct(\Phalcon\Mvc\Model $model = null)
    {
        $this->di = Phalcon\DI::getDefault();
        $this->_trans = $this->di->get('trans');
        $this->_action = substr($_SERVER['REQUEST_URI'], 1);
        $this->_model = $model;
        if ($this->_model !== null) {
            $this->_generateModelElements();
        }

        $this->init();
    }

    private function _generateModelElements()
    {
        $modelClass = new ReflectionClass($this->_model);

        foreach ($modelClass->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            if (in_array($property->getName(), $this->_ignoreFields) || substr($property->getName(), 0, 1) == "_") continue;

            $elementData = array(
                'label' => $this->_trans->query(ucfirst(str_replace('_', ' ', $property->getName()))),
                'filter' => $this->_getModelPropType($property),
                'value' => $this->_model->readAttribute($property->getName())
            );
            if (in_array($property->getName(), $this->_requiredFields)) {
                $elementData['required'] = true;
            }

            $fieldType = $this->_getModelFieldType($property);
            if ($fieldType == 'checkField' && !empty($elementData['value'])){
                $elementData['checked'] = true;
            }

            $this->addElement($fieldType, $property->getName(), $elementData);
        }

    }

    private function _getModelFieldType(ReflectionProperty $property)
    {
        preg_match_all('/@form_type\s+([^\s]+)/', $property->getDocComment(), $propData);
        $type = (count($propData) == 2 && !empty($propData[1][0]) ? $propData[1][0] : null);

        if ($type !== null)
            return $type;

        return "textField";
    }

    private function _getModelPropType(ReflectionProperty $property)
    {
        preg_match_all('/@var\s+([^\s]+)/', $property->getDocComment(), $propData);
        $type = (count($propData) == 2 && !empty($propData[1][0]) ? $propData[1][0] : null);

        if ($type !== null)
            return $type;

        return "string";
    }


    public function init()
    {

    }

    public function addElement($type, $name, $params = array(), $order = null)
    {
        if ($order === null) {
            $order = $this->_currentOrder++;
        }

        $this->_elements[$name] = array(
            "type" => $type,
            "name" => $name,
            "order" => $order,
            "params" => array_merge(array($name), $params)
        );

        return $this;
    }

    public function addButton($name, $isSubmit = false, $params = array())
    {
        $this->_buttons[$name] = array(
            "name" => $name,
            "is_submit" => $isSubmit,
            "params" => $params
        );

        return $this;
    }

    public function removeElement($name)
    {
        if (!empty($this->_elements[$name])) {
            unset($this->_elements[$name]);
        }
        return $this;
    }

    public function getElement($name)
    {
        if (empty($this->_elements[$name]))
            throw new Exception('Form has no element "' . $name . '"');

        return $this->_elements[$name];

    }

    public function setElementAttrib($name, $key, $value)
    {
        if (empty($this->_elements[$name]))
            throw new Exception('Form has no element "' . $name . '"');

        $this->_elements[$name][$key] = $value;

        return $this;
    }

    public function setElementParam($name, $key, $value)
    {
        if (empty($this->_elements[$name]))
            throw new Exception('Form has no element "' . $name . '"');

        $this->_elements[$name]['params'][$key] = $value;

        return $this;
    }

    public function setOption($key, $value)
    {
        if (property_exists($this, "_" . $key)) {
            $this->{"_" . $key} = $value;
        }

        return $this;
    }

    public function setAttrib($key, $value)
    {
        $this->_attribs[$key] = $value;
        return $this;
    }

    public function addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    public function addNotice($message)
    {
        $this->_notices[] = $message;
        return $this;
    }

    public function addRequired($value)
    {
        $this->_requiredFields[] = $value;
        return $this;
    }

    public function addIgnored($value)
    {
        $this->_ignoreFields[] = $value;
        return $this;
    }

    /**
     * @param \Phalcon\HTTP\RequestInterface $request
     *
     * @return bool
     */
    public function isValid($request)
    {
        if ($this->_useToken && !$this->di->get('security')->checkToken()) {
            $this->addError('Token is not valid!');
            return false;
        }


        $isValid = true;
        if ($this->_model !== null) {
            $modelClass = new ReflectionClass($this->_model);

            // fill model data
            foreach ($this->_elements as $element) {
                if ($modelClass->hasProperty($element['name'])) {
                    $varFilter = $this->_getModelPropType($modelClass->getProperty($element['name']));
                    $value = $request->getPost($element['name'], $varFilter, $this->_model->readAttribute($element['name']));
                    if (empty($value) && $this->_model->readAttribute($element['name']) === null) {
                        $value = $this->_model->readAttribute($element['name']);
                    }
                    if ($element['type'] == 'checkField'){
                        $value = $request->get($element['name']);
                    }
                    $this->_model->writeAttribute($element['name'], $value);

                    $this->_data[$element['name']] = $value;
                    $this->_elements[$element['name']]['params']['value'] = $value;
                }

            }

            // validate model data
            $isValid = $this->_model->save();
            if (!$isValid) {
                foreach ($this->_model->getMessages() as $message) {
                    $this->addError($message);
                }
            }
        } else {
            foreach ($this->_elements as $element) {
                if ((!empty($element['params']['ignore']) && $element['params']['ignore'] == true) || in_array($element['name'], $this->_ignoreFields) || in_array($element['type'], $this->_ignoreTypes)) continue;

                // get value
                $value = $request->getPost($element['name'], (!empty($element['params']['filter']) ? $element['params']['filter'] : null));

                // check if this field is required and not empty
                if (!empty($element['params']['required']) && $element['params']['required'] == true && (!$request->hasPost($element['name']) || preg_match('/^\s+$/s', $value))) {
                    $label = (!empty($element['params']['label']) ? $element['params']['label'] : $element['name']);
                    $this->addError(sprintf(self::MESSAGE_FIELD_REQUIRED, $label));
                    $isValid = false;
                    continue;
                }

                // check validators
                if (!empty($element['params']['validators']) && is_array($element['params']['validators'])) {
                    foreach ($element['params']['validators'] as $validator) {
                        if (!($validator instanceof Validator_Abstract)) continue;
                        if (!$validator->isValid($value)) {
                            foreach ($validator->getMessages() as $message) {
                                $this->addError($message);
                            }
                            $isValid = false;
                        }
                    }
                }

                $this->_data[$element['name']] = $value;
                $this->_elements[$element['name']]['params']['value'] = $value;
            }
        }

        return $isValid;
    }

    public function getData()
    {
        if ($this->_model !== null) {
            return $this->_model;
        }

        return $this->_data;
    }

    public function setData($data)
    {
        if (!empty($this->_elements)) {
            foreach ($data as $key => $value) {
                if (!empty($this->_elements[$key]))
                    $this->_elements[$key]['params']['value'] = $value;

            }
        }

        return $this;
    }


    public function render()
    {
        if (empty($this->_elements)) return "";
        $tagReflection = new ReflectionClass("Phalcon\Tag");

        // sort elements by order
        usort($this->_elements, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        $body = Tag::form(array_merge($this->_attribs, array($this->_action, 'method' => $this->_method, 'ectype' => $this->_enctype))) . '<div>';

        // title and description
        if (!empty($this->_title) || !empty($this->_description)) {
            $body .= sprintf('<div class="form_header"><h3>%s</h3><p>%s</p></div>', $this->_trans->_($this->_title), $this->_trans->_($this->_description));
        }

        // error messages
        if (!empty($this->_errors)) {
            $body .= '<ul class="form_errors">';
            foreach ($this->_errors as $error) {
                $body .= sprintf('<li class="alert alert-error">%s</li>', $this->_trans->_($error));
            }
            $body .= '</ul>';
        }

        // notice messages
        if (!empty($this->_notices)) {
            $body .= '<ul class="form_notices">';
            foreach ($this->_notices as $notice) {
                $body .= sprintf('<li class="alert alert-success">%s</li>', $this->_trans->_($notice));
            }
            $body .= '</ul>';
        }

        $body .= '<div class="form_elements">';

        foreach ($this->_elements as $element) {
            if (!$tagReflection->hasMethod($element['type'])) continue;
            $body .= '<div>';
            if (!empty($element['params']['label']) || !empty($element['params']['description'])) {
                $label = (!empty($element['params']['label']) ? sprintf('<label for="%s">%s</label>', $element['name'], $element['params']['label']) : '');
                $description = (!empty($element['params']['description']) ? sprintf('<p>%s</p>', $element['params']['description']) : '');
                $body .= sprintf('<div class="form_label">%s%s</div>', $label, $description);
            }
            if ($element['type'] == "select" || $element['type'] == "selectStatic") {
                if (!empty($element['params']['options'])){
                    $value = (!empty($element['params']['value'])?$element['params']['value']:null);
                    $body .= sprintf('<div class="form_element">%s</div>', Tag::$element['type'](array($element['name'], $element['params']['options'], 'value' => $value)));
                }
            } else {
                unset($element['params']['validators']); // Phalcon elements doesn't like this
                unset($element['params']['filter']);

                $body .= sprintf('<div class="form_element">%s</div>', Tag::$element['type']($element['params']));
            }
            $body .= '</div>';
        }

        $body .= '</div>';

        if ($this->_useToken) {
            $tokenKey = $this->di->get('security')->getTokenKey();
            $token = $this->di->get('security')->getToken();
            $body .= sprintf('<input type="hidden" name="%s" value="%s">', $tokenKey, $token);
        }

        if (!empty($this->_buttons)) {
            $body .= '<div class="form_footer">';
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

                $body .= sprintf('<button%s%s>%s</button>', ($button['is_submit'] === true ? ' type="submit"' : ''), $attribs, $this->_trans->_($button['name']));

            }
            $body .= '</div>';
        }

        $body .= '</div>' . Tag::endForm();

        return $body;
    }
}