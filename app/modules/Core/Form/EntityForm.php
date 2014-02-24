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

namespace Core\Form;

use Engine\Behaviour\TranslationBehaviour;
use Engine\Db\AbstractModel;
use Engine\Form\AbstractElement;
use Engine\Form\AbstractForm;
use Engine\Form\Behaviour\ContainerBehaviour;
use Engine\Form\Behaviour\FormBehaviour;
use Phalcon\DI;
use Phalcon\Filter;
use Phalcon\Tag as Tag;
use Phalcon\Translate;
use Phalcon\Validation;

/**
 * Entity form trait.
 *
 * @category  PhalconEye
 * @package   Core\Form
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait EntityForm
{
    /**
     * Create form according to entity specifications.
     *
     * @param AbstractModel[] $entities      Models.
     * @param array           $fieldTypes    Field types.
     * @param array           $excludeFields Exclude fields from form.
     *
     * @return AbstractForm
     */
    public static function factory($entities, array $fieldTypes = [], array $excludeFields = [])
    {
        $form = new static();
        if (!is_array($entities)) {
            $entities = [$entities];
        }

        /** @var AbstractModel $entity */
        foreach ($entities as $entityKey => $entity) {
            $types = (isset($fieldTypes[$entityKey]) ? $fieldTypes[$entityKey] : []);
            $exclude = (isset($excludeFields[$entityKey]) ? $excludeFields[$entityKey] : []);

            foreach ($entity->toArray() as $key => $value) {
                if (in_array($key, $exclude)) {
                    continue;
                }

                $elementClass = '\Engine\Form\Element\\' . (isset($types[$key]) ? $types[$key] : 'Text');
                /** @var AbstractElement $element */
                $element = new $elementClass($key);
                $element
                    ->setOption('label', ucfirst(str_replace('_', ' ', $key)))
                    ->setValue($value);

                $form->add($element);
            }
        }

        return $form;
    }
}