<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                        |
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
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
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
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
                $arguments = $annotation->getArguments();

                switch ($annotation->getName()) {
                    /**
                     * Initializes the model's source
                     */
                    case 'Source':
                        $manager->setModelSource($model, $arguments[0]);
                        break;

                    /**
                     * Initializes Has-Many relations
                     */
                    case 'HasMany':
                        array_unshift($arguments, $model);
                        call_user_func_array([$manager, 'addHasMany'], $arguments);
                        break;

                    /**
                     * Initializes Has-Many-To-Many relations
                     */
                    case 'HasManyToMany':
                        array_unshift($arguments, $model);
                        call_user_func_array([$manager, 'addHasManyToMany'], $arguments);
                        break;

                    /**
                     * Initializes BelongsTo relations
                     */
                    case 'BelongsTo':
                        array_unshift($arguments, $model);
                        call_user_func_array([$manager, 'addBelongsTo'], $arguments);
                        break;
                }
            }
        }

        return $event->getType();
    }
}