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
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine;

use Engine\Behavior\DIBehavior;
use Engine\Behavior\ViewBehavior;
use Engine\View\Extension;
use Engine\View\Plugin\ViewPlugin;
use Phalcon\Cache\BackendInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Volt;

/**
 * View factory.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class View extends PhalconView
{
    use ViewBehavior;

    /**
     * Last picked view was final?
     *
     * @var bool
     */
    protected $_finalView = false;

    /**
     * Current view that is rendering.
     *
     * @var string|null
     */
    protected $_currentView = null;

    /**
     * Create view instance.
     * If no events manager provided - events would not be attached.
     *
     * @param DIBehavior   $di             DI.
     * @param Config       $config         Configuration.
     * @param string|null  $viewsDirectory Views directory location.
     * @param Manager|null $em             Events manager.
     *
     * @return View
     */
    public static function factory($di, $config, $viewsDirectory = null, $em = null)
    {
        $view = new View();
        $volt = new Volt($view, $di);
        $volt->setOptions(
            [
                'compiledPath' => $config->application->view->compiledPath,
                'compiledExtension' => $config->application->view->compiledExtension,
                'compiledSeparator' => $config->application->view->compiledSeparator,
                'compileAlways' => $config->application->debug && $config->application->view->compileAlways
            ]
        );

        $compiler = $volt->getCompiler();
        $compiler->addExtension(new Extension());
        $view
            ->restoreViewDir()
            ->registerEngines([".volt" => $volt])
            ->setRenderLevel(View::LEVEL_ACTION_VIEW);

        if (!empty($viewsDirectory)) {
            $view->setViewsDir($viewsDirectory);
        }

        $di->getRegistry()->offsetSet('viewRendered', []);
        // Attach a listener for type "view".
        if ($em) {
            $em->attach('view', new ViewPlugin());
            $view->setEventsManager($em);
        }

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    protected function _engineRender($engines, $viewPath, $silence, $mustClean, BackendInterface $cache = null)
    {
        $this->_currentView = $viewPath;
        parent::_engineRender($engines, $viewPath, $silence, $mustClean, $cache);
    }


    /**
     * Pick view to render.
     *
     * @param array|string $renderView View to render.
     * @param string|null  $module     Specify module.
     * @param bool|null    $finalView  This view will be final in pick process.
     *
     * @return $this
     */
    public function pick($renderView, $module = null, $finalView = null)
    {
        if ($finalView !== null) {
            $this->_finalView = $finalView;
        }

        parent::pick($this->resolveView($renderView, $module));
    }

    /**
     * Restore basic view directory.
     *
     * @return $this
     */
    public function restoreViewDir()
    {
        $registry = $this->getDI()->getRegistry();
        $theme = $this->getDI()->getAssets()->getTheme();
        return $this->setViewsDir(
            [
                $registry->directories->themes . $theme . DS . 'views',
                $registry->directories->modules,
                $registry->directories->cms
            ]
        );
    }

    /**
     * Pick default view according on current dispatcher parameters about
     * current controller and action.
     *
     * If another view was picked up with final view flag 'true' - this method will not work.
     *
     * @return $this
     */
    public function pickDefaultView()
    {
        if ($this->_finalView) {
            return $this;
        }

        $dispatcher = $this->getDI()->getDispatcher();
        $router = $this->getDI()->getRouter();

        return $this->pick(
            $dispatcher->getControllerName() . '/' . $dispatcher->getActionName(),
            $router->getModuleName()
        );
    }

    /**
     * Current view that is rendering.
     *
     * @return null|string
     */
    public function getCurrentView()
    {
        return $this->_currentView;
    }

    /**
     * Get current rendering path.
     *
     * @return string
     */
    public function getCurrentPath()
    {
        $path = $this->getActiveRenderPath();
        if (is_array($path) && !empty($path)) {
            return $path[0];
        }

        return $path;
    }
}