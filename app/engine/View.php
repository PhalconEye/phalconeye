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

namespace Engine;

use Engine\Behaviour\DIBehaviour;
use Engine\View\Extension;
use Phalcon\DI;
use Phalcon\Events\Manager;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Volt;

/**
 * View factory.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class View extends PhalconView
{
    /**
     * Create view instance.
     * If no events manager provided - events would not be attached.
     *
     * @param DIBehaviour  $di             DI.
     * @param Config       $config         Configuration.
     * @param string       $viewsDirectory Views directory location.
     * @param Manager|null $em             Events manager.
     *
     * @return View
     */
    public static function factory($di, $config, $viewsDirectory, $em = null)
    {
        $view = new View();
        $volt = new Volt($view, $di);
        $volt->setOptions(
            [
                "compiledPath" => $config->application->view->compiledPath,
                "compiledExtension" => $config->application->view->compiledExtension,
                'compiledSeparator' => $config->application->view->compiledSeparator,
                'compileAlways' => $config->application->debug && $config->application->view->compileAlways
            ]
        );

        $compiler = $volt->getCompiler();
        $compiler->addExtension(new Extension());
        $view
            ->registerEngines([".volt" => $volt])
            ->setRenderLevel(View::LEVEL_ACTION_VIEW)
            ->setViewsDir($viewsDirectory);

        // Attach a listener for type "view".
        if ($em) {
            $em->attach(
                "view",
                function ($event, $view) use ($di, $config) {
                    if ($config->application->profiler && $di->has('profiler')) {
                        if ($event->getType() == 'beforeRender') {
                            $di->get('profiler')->start();
                        }
                        if ($event->getType() == 'afterRender') {
                            $di->get('profiler')->stop($view->getActiveRenderPath(), 'view');
                        }
                    }
                    if ($event->getType() == 'notFoundView') {
                        throw new Exception('View not found - "' . $view->getActiveRenderPath() . '"');
                    }
                }
            );
            $view->setEventsManager($em);
        }

        return $view;
    }

    /**
     * Pick view to render.
     *
     * @param array|string $renderView View to render.
     * @param string|null  $module     Specify module.
     *
     * @return PhalconView|void
     */
    public function pick($renderView, $module = null)
    {
        if ($module != null) {
            $renderView = '../../' . ucfirst($module) . '/View/' . $renderView;
        }

        parent::pick($renderView);
    }
}