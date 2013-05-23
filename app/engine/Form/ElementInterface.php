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

namespace Engine\Form;

interface ElementInterface{

    /**
     * \Phalcon\Forms\Element constructor
     *
     * @param string $name
     * @param array $attributes
     */
    public function __construct($name, $attributes=null);

    /**
     * If element is need to be rendered in default layout
     *
     * @return bool
     */
    public function useDefaultLayout();

    /**
     * Sets the element description
     *
     * @param string $description
     * @return \Engine\Form\ElementInterface
     */
    public function setDescription($description);


    /**
     * Returns the element's description
     *
     * @return string
     */
    public function getDescription();


    /**
     * Sets the parent form to the element
     *
     * @param \Phalcon\Forms\Form $form
     * @return \Phalcon\Forms\Form
     */
    public function setForm($form);


    /**
     * Returns the parent form to the element
     *
     * @return \Phalcon\Forms\Form
     */
    public function getForm();


    /**
     * Sets the element's name
     *
     * @param string $name
     * @return \Phalcon\Forms\Form
     */
    public function setName($name);


    /**
     * Returns the element's name
     *
     * @return string
     */
    public function getName();


    /**
     * Adds a group of validators
     *
     * @param \Phalcon\Validation\ValidatorInterface[]
     * @return \Phalcon\Forms\Form
     */
    public function addValidators($validators, $merge=null);


    /**
     * Adds a validator to the element
     *
     * @param \Phalcon\Validation\ValidatorInterface
     * @return \Phalcon\Forms\Form
     */
    public function addValidator($validator);


    /**
     * Returns the validators registered for the element
     *
     * @return \Phalcon\Validation\ValidatorInterface[]
     */
    public function getValidators();


    /**
     * Returns an array of attributes for \Phalcon\Tag helpers prepared
     * according to the element's parameters
     *
     * @param array $attributes
     * @return array
     */
    public function prepareAttributes($attributes);


    /**
     * Sets a default attribute for the element
     *
     * @param string $attribute
     * @param mixed $value
     * @return \Phalcon\Forms\Form
     */
    public function setAttribute($attribute, $value);


    /**
     * Sets default attributes for the element
     *
     * @param array $attributes
     * @return \Phalcon\Forms\Form
     */
    public function setAttributes($attributes);


    /**
     * Returns the default attributes for the element
     *
     * @return array
     */
    public function getAttributes();


    /**
     * Sets the element label
     *
     * @param string $label
     * @return \Phalcon\Forms\Form
     */
    public function setLabel($label);


    /**
     * Returns the element's label
     *
     * @return string
     */
    public function getLabel();


    /**
     * Magic method __toString renders the widget without atttributes
     *
     * @return string
     */
    public function __toString();

}