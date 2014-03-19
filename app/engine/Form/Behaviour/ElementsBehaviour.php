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
use Engine\Form\AbstractElement;
use Engine\Form\Element\File as FileElement;
use Phalcon\DI;
use Phalcon\Mvc\View;

/**
 * Elements behaviour.
 * Method for simple element creation.
 *
 * @category  PhalconEye
 * @package   Engine\Form\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 */
trait ElementsBehaviour
{
    /**
     * Add element to form.
     *
     * @param AbstractElement $element Element object.
     * @param int|null        $order   Element order.
     *
     * @return $this
     */
    abstract public function add(AbstractElement $element, $order = null);

    /**
     * Html element.
     *
     * @param string     $name    Element name.
     * @param mixed|null $value   Element value.
     * @param bool|array $partial Value is partial path? This also used as partial variables.
     *
     * @return $this
     */
    public function addHtml($name, $value = null, $partial = false)
    {
        $element = new Form\Element\Html($name);
        if ($partial !== false) {
            if (!is_array($partial)) {
                $partial = ['form' => $this];
            } else {
                $partial = array_merge(['form' => $this], $partial);
            }

            /** @var View $view */
            $view = $this->getDI()->get('view');
            ob_start();
            $view->partial($value, $partial);
            $html = ob_get_contents();
            ob_end_clean();
            $element->setValue($html);
        } else {
            $element->setValue($value);
        }
        $this->add($element);

        return $this;
    }

    /**
     * Button element.
     *
     * @param string     $name       Element name.
     * @param null       $label      Element label.
     * @param bool       $isSubmit   Button type 'submit'.
     * @param mixed|null $value      Element value.
     * @param array      $options    Element options.
     * @param array      $attributes Element attributes.
     *
     * @return $this
     */
    public function addButton(
        $name,
        $label = null,
        $isSubmit = true,
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\Button($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('isSubmit', $isSubmit)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * ButtonLink element.
     *
     * @param string     $name       Element name.
     * @param null       $label      Element label.
     * @param mixed|null $value      Element link.
     * @param array      $options    Element options.
     * @param array      $attributes Element attributes.
     *
     * @return $this
     */
    public function addButtonLink($name, $label = null, $value = null, array $options = [], array $attributes = [])
    {
        $element = new Form\Element\ButtonLink($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * Text element.
     *
     * @param string      $name        Element name.
     * @param string|null $label       Element label.
     * @param string|null $description Element description.
     * @param mixed|null  $value       Element value.
     * @param array       $options     Element options.
     * @param array       $attributes  Element attributes.
     *
     * @return $this
     */
    public function addText(
        $name,
        $label = null,
        $description = null,
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\Text($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * TextArea element.
     *
     * @param string      $name        Element name.
     * @param string|null $label       Element label.
     * @param string|null $description Element description.
     * @param mixed|null  $value       Element value.
     * @param array       $options     Element options.
     * @param array       $attributes  Element attributes.
     *
     * @return $this
     */
    public function addTextArea(
        $name,
        $label = null,
        $description = null,
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\TextArea($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * CkEditor element.
     *
     * @param string      $name           Element name.
     * @param string|null $label          Element label.
     * @param string|null $description    Element description.
     * @param array|null  $elementOptions CkEditor options.
     * @param mixed|null  $value          Element value.
     * @param array       $options        Element options.
     * @param array       $attributes     Element attributes.
     *
     * @return $this
     */
    public function addCkEditor(
        $name,
        $label = null,
        $description = null,
        $elementOptions = [],
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\CkEditor($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('elementOptions', $elementOptions)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * Password element.
     *
     * @param string      $name        Element name.
     * @param string|null $label       Element label.
     * @param string|null $description Element description.
     * @param array       $options     Element options.
     * @param array       $attributes  Element attributes.
     *
     * @return $this
     */
    public function addPassword($name, $label = null, $description = null, array $options = [], array $attributes = [])
    {
        $element = new Form\Element\Password($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description);
        $this->add($element);

        return $this;
    }

    /**
     * Hidden element.
     *
     * @param string     $name       Element name.
     * @param mixed|null $value      Element value.
     * @param array      $options    Element options.
     * @param array      $attributes Element attributes.
     *
     * @return $this
     */
    public function addHidden($name, $value = null, array $options = [], array $attributes = [])
    {
        $element = new Form\Element\Hidden($name, $options, $attributes);
        $element->setValue($value);
        $this->add($element);

        return $this;
    }

    /**
     * Heading element.
     *
     * @param string     $name       Element name.
     * @param mixed|null $value      Element value.
     * @param array      $options    Element options.
     * @param array      $attributes Element attributes.
     *
     * @return $this
     */
    public function addHeading($name, $value = null, array $options = [], array $attributes = [])
    {
        $element = new Form\Element\Heading($name, $options, $attributes);
        $element->setValue($value);
        $this->add($element);

        return $this;
    }

    /**
     * File element.
     *
     * @param string      $name        Element name.
     * @param string|null $label       Element label.
     * @param string|null $description Element description.
     * @param bool        $isImage     File must be an image.
     * @param mixed|null  $value       Element value.
     * @param array       $options     Element options.
     * @param array       $attributes  Element attributes.
     *
     * @return $this
     */
    public function addFile(
        $name,
        $label = null,
        $description = null,
        $isImage = false,
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new FileElement($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('isImage', $isImage)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * RemoteFile element.
     *
     * @param string      $name        Element name.
     * @param string|null $label       Element label.
     * @param string|null $description Element description.
     * @param mixed|null  $value       Element value.
     * @param array       $options     Element options.
     * @param array       $attributes  Element attributes.
     *
     * @return $this
     */
    public function addRemoteFile(
        $name,
        $label = null,
        $description = null,
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\RemoteFile($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setValue($value);

        $this->add($element);

        return $this;
    }

    /**
     * Checkbox element.
     *
     * @param string      $name         Element name.
     * @param string|null $label        Element label.
     * @param string|null $description  Element description.
     * @param mixed|null  $value        Element value.
     * @param bool        $isChecked    Element is checked.
     * @param mixed       $defaultValue Element default value.
     * @param array       $options      Element options.
     * @param array       $attributes   Element attributes.
     *
     * @return $this
     */
    public function addCheckbox(
        $name,
        $label = null,
        $description = null,
        $value = null,
        $isChecked = false,
        $defaultValue = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\Checkbox($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('checked', $isChecked)
            ->setOption('defaultValue', $defaultValue)
            ->setAttribute('value', $value);
        $this->add($element);

        return $this;
    }

    /**
     * Radio element.
     *
     * @param string      $name           Element name.
     * @param string|null $label          Element label.
     * @param string|null $description    Element description.
     * @param array       $elementOptions Element value options.
     * @param mixed|null  $value          Element value.
     * @param array       $options        Element options.
     * @param array       $attributes     Element attributes.
     *
     * @return $this
     */
    public function addRadio(
        $name,
        $label = null,
        $description = null,
        $elementOptions = [],
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\Radio($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('elementOptions', $elementOptions)
            ->setValue($value);
        $this->add($element);

        return $this;
    }

    /**
     * MultiCheckbox element.
     *
     * @param string      $name           Element name.
     * @param string|null $label          Element label.
     * @param string|null $description    Element description.
     * @param array       $elementOptions Element value options.
     * @param mixed|null  $value          Element value.
     * @param array       $options        Element options.
     * @param array       $attributes     Element attributes.
     *
     * @return $this
     */
    public function addMultiCheckbox(
        $name,
        $label = null,
        $description = null,
        $elementOptions = [],
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\MultiCheckbox($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('elementOptions', $elementOptions)
            ->setValue($value);
        $this->add($element);

        return $this;
    }

    /**
     * Select element.
     *
     * @param string      $name           Element name.
     * @param string|null $label          Element label.
     * @param string|null $description    Element description.
     * @param array       $elementOptions Element value options.
     * @param mixed|null  $value          Element value.
     * @param array       $options        Element options.
     * @param array       $attributes     Element attributes.
     *
     * @return $this
     */
    public function addSelect(
        $name,
        $label = null,
        $description = null,
        $elementOptions = [],
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $element = new Form\Element\Select($name, $options, $attributes);

        if (!$label) {
            $label = ucfirst($name);
        }

        $element
            ->setOption('label', $label)
            ->setOption('description', $description)
            ->setOption('elementOptions', $elementOptions)
            ->setValue($value);
        $this->add($element);

        return $this;
    }


    /**
     * Select element with 'multiple' attribute.
     *
     * @param string      $name           Element name.
     * @param string|null $label          Element label.
     * @param string|null $description    Element description.
     * @param array       $elementOptions Element value options.
     * @param mixed|null  $value          Element value.
     * @param array       $options        Element options.
     * @param array       $attributes     Element attributes.
     *
     * @return $this
     */
    public function addMultiSelect(
        $name,
        $label = null,
        $description = null,
        $elementOptions = [],
        $value = null,
        array $options = [],
        array $attributes = []
    )
    {
        $attributes['id'] = $name;
        $attributes['multiple'] = 'multiple';
        $name = $name . '[]';
        return $this->addSelect($name, $label, $description, $elementOptions, $value, $options, $attributes);
    }
}