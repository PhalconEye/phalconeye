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

namespace Engine\Plugin;

class CacheAnnotation extends \Phalcon\Mvc\User\Plugin
{

    /**
     * This event is executed before every route is executed in the dispatcher
     *
     */
    public function beforeExecuteRoute($event, $dispatcher)
    {
        //Parse the annotations in the method currently executed
        $annotations = $this->annotations->getMethod(
            $dispatcher->getActiveController(),
            $dispatcher->getActiveMethod()
        );

        //Check if the method has an annotation 'Cache'
        if ($annotations->has('Cache')) {

            //The method has the annotation 'Cache'
            /** @var \Phalcon\Annotations\Annotation $annotation */
            $annotation = $annotations->get('Cache');

            //Get the lifetime
            $lifetime = $annotation->getNamedParameter('lifetime');

            $options = array('lifetime' => $lifetime);

            //Check if there is a user defined cache key
            if ($annotation->hasNamedArgument('key')) {
                $options['key'] = $annotation->getNamedParameter('key');
            }

            //Enable the cache for the current method
            $this->view->cache($options);
        }

    }

}