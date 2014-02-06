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

namespace Engine\Db\Model\Annotations;

use Engine\Db\AbstractModel;
use Phalcon\Events\Event;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\User\Plugin;

/**
 * Annotations initializer.
 *
 * @category  PhalconEye
 * @package   Engine\Db\Model\Annotations
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Initializer extends Plugin
{
    /**
     * This is called after initialize the model.
     *
     * @param Event         $event   Event object.
     * @param ModelsManager $manager Model manager
     * @param AbstractModel $model   Model object.
     *
     * @return string
     */
    public function afterInitialize(Event $event, ModelsManager $manager, $model)
    {
        //Reflector
        $reflector = $this->annotations->get($model);

        /**
         * Read the annotations in the class' docblock
         */
        $annotations = $reflector->getClassAnnotations();
        if ($annotations) {
            /**
             * Traverse the annotations
             */
            foreach ($annotations as $annotation) {
                switch ($annotation->getName()) {
                    /**
                     * Initializes the model's source
                     */
                    case 'Source':
                        $arguments = $annotation->getArguments();
                        $manager->setModelSource($model, $arguments[0]);
                        break;

                    /**
                     * Initializes Has-Many relations
                     */
                    case 'HasMany':
                        $arguments = $annotation->getArguments();
                        if (isset($arguments[3])) {
                            $manager->addHasMany($model, $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                        } else {
                            $manager->addHasMany($model, $arguments[0], $arguments[1], $arguments[2]);
                        }
                        break;

                    /**
                     * Initializes BelongsTo relations
                     */
                    case 'BelongsTo':
                        $arguments = $annotation->getArguments();
                        if (isset($arguments[3])) {
                            $manager->addBelongsTo($model, $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                        } else {
                            $manager->addBelongsTo($model, $arguments[0], $arguments[1], $arguments[2]);
                        }
                        break;
                }
            }
        }

        return $event->getType();
    }
}