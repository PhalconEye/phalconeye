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

use Engine\Db\AbstractModel;
use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form\Behaviour\FieldSetBehaviour;
use Engine\Form;
use Phalcon\Validation as PhalconValidation;


/**
 * Validation class.
 *
 * @category  PhalconEye
 * @package   Engine\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Validation extends PhalconValidation
{
    /**
     * Related form object.
     *
     * @var ContainerBehaviour
     */
    protected $_container;

    /**
     * Validators.
     *
     * @var array
     */
    protected $_preparedValidators = [];

    /**
     * Create validation.
     *
     * @param ContainerBehaviour $container  Form object.
     * @param array|null         $validators prepared validators.
     */
    public function __construct($container, $validators = null)
    {
        $this->_container = $container;
        parent::__construct($validators);
    }

    /**
     * Add validator to validation.
     *
     * @param string                      $attribute Attribute name.
     * @param PhalconValidation\Validator $validator Validator object.
     *
     * @throws Exception
     * @return $this
     */
    public function add($attribute, $validator)
    {
        if (!$this->_container->has($attribute)) {
            throw new Exception(sprintf('Element "%s" not found in current elements container.', $attribute));
        }
        $this->_preparedValidators[$attribute][] = $validator;
        return $this;
    }

    /**
     * Remove validator from validation.
     *
     * @param string $attribute Attribute name.
     *
     * @return $this
     */
    public function remove($attribute)
    {
        unset($this->_preparedValidators[$attribute]);
        return $this;
    }

    /**
     * Validate.
     *
     * @param array|null         $data   Data to validate.
     * @param AbstractModel|null $entity Entity to validate.
     *
     * @return PhalconValidation\Message\Group|void
     */
    public function validate($data = null, $entity = null)
    {
        if (empty($this->_preparedValidators)) {
            return new PhalconValidation\Message\Group();
        }

        foreach ($this->_preparedValidators as $attribute => $validators) {
            foreach ($validators as $validator) {
                parent::add($attribute, $validator);
            }
        }

        return parent::validate($data, $entity);
    }

    /**
     * Clear all validators.
     *
     * @return $this
     */
    public function clearValidators()
    {
        $this->_preparedValidators = [];
        return $this;
    }
}