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

namespace Engine\Plugin;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * Cache plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class CacheAnnotation extends PhalconPlugin
{
    /**
     * This event is executed before every route is executed in the dispatcher.
     *
     * @param Event      $event      Event object.
     * @param Dispatcher $dispatcher Dispatcher object.
     *
     * @return bool
     */
    public function beforeExecuteRoute($event, $dispatcher)
    {
        // Parse the annotations in the method currently executed.
        $annotations = $this->annotations->getMethod(
            $dispatcher->getActiveController(),
            $dispatcher->getActiveMethod()
        );

        // Check if the method has an annotation 'Cache'.
        if ($annotations->has('Cache')) {

            // The method has the annotation 'Cache'.
            /** @var \Phalcon\Annotations\Annotation $annotation */
            $annotation = $annotations->get('Cache');

            // Get the lifetime.
            $lifetime = $annotation->getNamedArgument('lifetime');

            $options = ['lifetime' => $lifetime];

            // Check if there is a user defined cache key.
            if ($annotation->hasNamedArgument('key')) {
                $options['key'] = $annotation->getNamedArgument('key');
            }

            // Enable the cache for the current method.
            $this->view->cache($options);
        }

        return !$event->isStopped();
    }

}