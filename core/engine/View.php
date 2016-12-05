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
use Engine\Plugin\ViewPlugin;
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
    const
        PATH_VIEW = 'View',
        PATH_WIDGET = 'Widget',
        PATH_BACKOFFICE = 'Backoffice',
        PATH_THEME_VIEWS = 'views';

    use ViewBehavior;

    /**
     * View must be searched in current module?
     * Null -- search everywhere.
     *
     * @var null
     */
    protected $_currentModule = null;

    /**
     * Current view that is rendering.
     *
     * @var string|null
     */
    protected $_currentView = null;

    /**
     * Flag will be true if view was manually picked.
     *
     * @var bool
     */
    protected $_viewPicked = false;

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
                'compiledPath' => $config->core->view->compiledPath,
                'compiledExtension' => $config->core->view->compiledExtension,
                'compiledSeparator' => $config->core->view->compiledSeparator,
                'compileAlways' => $config->application->debug && $config->core->view->compileAlways
            ]
        );

        $compiler = $volt->getCompiler();
        $compiler->addExtension(new Extension());
        $view
            ->restoreViewDirectories()
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
     * @param array|string $renderView  View to render.
     * @param string|null  $module      Specify module.
     * @param bool         $resolveView Resolve this view to module?
     *
     * @return $this
     */
    public function pick($renderView, $module = null, $resolveView = true)
    {
        $this->_viewPicked = true;
        if ($resolveView) {
            $renderView = $this->resolveView($renderView, $module != null ? $module : $this->getCurrentModule());
        }
        parent::pick($renderView);
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
        if ($this->_viewPicked) {
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
     * Restore basic view directories.
     *
     * @return \Phalcon\Mvc\View|View
     */
    public function restoreViewDirectories()
    {
        $registry = $this->getDI()->getRegistry();
        $theme = $this->getDI()->getAssets()->getTheme();
        return $this->setViewsDir(
            [
                $registry->directories->themes . $theme . DS . self::PATH_THEME_VIEWS,
                $registry->directories->modules,
                $registry->directories->cms
            ]
        );
    }

    /**
     * Set current module.
     *
     * @param string $currentModule Current module name.
     */
    public function setCurrentModule($currentModule)
    {
        $this->_currentModule = $currentModule;
    }

    /**
     * Get current module.
     *
     * @return string
     */
    public function getCurrentModule()
    {
        return $this->_currentModule;
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