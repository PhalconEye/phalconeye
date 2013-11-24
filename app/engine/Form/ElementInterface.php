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

namespace Engine\Form;

use Engine\Form;
use Phalcon\Validation\ValidatorInterface;

/**
 * Form element interface.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
interface ElementInterface
{
    /**
     * Element constructor.
     *
     * @param string $name       Element name.
     * @param array  $attributes Element attributes.
     */
    public function __construct($name, $attributes = null);

    /**
     * If element is need to be rendered in default layout.
     *
     * @return bool
     */
    public function useDefaultLayout();

    /**
     * Sets the element description.
     *
     * @param string $description Element description text.
     *
     * @return ElementInterface
     */
    public function setDescription($description);

    /**
     * Returns the element's description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the parent form to the element.
     *
     * @param Form $form Form object.
     *
     * @return Form
     */
    public function setForm($form);

    /**
     * Returns the parent form to the element.
     *
     * @return Form
     */
    public function getForm();

    /**
     * Sets the element's name.
     *
     * @param string $name Element naming.
     *
     * @return Form
     */
    public function setName($name);

    /**
     * Returns the element's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Adds a group of validators.
     *
     * @param ValidatorInterface[] $validators Validators array.
     * @param bool                 $merge      Merge with existing or not.
     *
     * @return Form
     */
    public function addValidators($validators, $merge = null);

    /**
     * Adds a validator to the element.
     *
     * @param ValidatorInterface $validator Validator object.
     *
     * @return \Phalcon\Forms\Form
     */
    public function addValidator($validator);

    /**
     * Returns the validators registered for the element.
     *
     * @return ValidatorInterface[]
     */
    public function getValidators();

    /**
     * Returns an array of attributes for \Phalcon\Tag helpers prepared
     * according to the element's parameters.
     *
     * @param array $attributes Element attributes.
     *
     * @return array
     */
    public function prepareAttributes($attributes, $useChecked=null);

    /**
     * Sets a default attribute for the element.
     *
     * @param string $attribute Attribute name.
     * @param mixed  $value     Attribute value.
     *
     * @return Form
     */
    public function setAttribute($attribute, $value);

    /**
     * Sets default attributes for the element..
     *
     * @param array $attributes Attributes array.
     *
     * @return Form
     */
    public function setAttributes($attributes);

    /**
     * Returns the default attributes for the element
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Sets the element label.
     *
     * @param string $label Label text.
     *
     * @return Form
     */
    public function setLabel($label);

    /**
     * Returns the element's label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Magic method __toString renders the widget without attributes.
     *
     * @return string
     */
    public function __toString();
}