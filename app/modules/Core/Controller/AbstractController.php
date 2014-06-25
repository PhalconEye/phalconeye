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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller;

use Core\Controller\Traits\JsTranslations;
use Core\Helper\Renderer;
use Core\Model\Page;
use Engine\Asset\Manager as AssetManager;
use Engine\Behaviour\DIBehaviour;
use Engine\Exception;
use Phalcon\Db\Column;
use Phalcon\DI;
use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View;

/**
 * Base controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @property \Phalcon\Db\Adapter\Pdo    $db
 * @property \Phalcon\Cache\Backend     $cacheData
 * @property \Engine\Application        $app
 * @property \Engine\View               $view
 * @property \Engine\Asset\Manager      $assets
 * @property \Engine\Config             $config
 * @property \Phalcon\Translate\Adapter $i18n
 * @property DIBehaviour|DI             $di
 *
 * @method \Engine\DIBehaviour|\Phalcon\DI getDI()
 */
abstract class AbstractController extends PhalconController
{
    use JsTranslations;

    /**
     * Initializes the controller.
     *
     * @return void
     */
    public function initialize()
    {
        if ($this->di->has('profiler')) {
            $this->profiler->start();
        }

        if (!$this->request->isAjax()) {
            $this->_setupAssets();
        }

        // run init function
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * After route execution.
     *
     * @return void
     */
    public function afterExecuteRoute()
    {
        if ($this->di->has('profiler')) {
            $this->profiler->stop(get_called_class(), 'controller');
        }
    }

    /**
     * Render some content from database layout.
     *
     * @param null|string $url        Url definition of page.
     * @param null|string $controller Related controller name.
     * @param null|string $type       Page type.
     *
     * @return mixed
     */
    public function renderContent($url = null, $controller = null, $type = null)
    {
        $page = null;
        if ($url !== null) {
            $page = Page::find(
                [
                    'conditions' => 'url=:url1: OR url=:url2: OR id = :url3:',
                    'bind' => ["url1" => $url, "url2" => '/' . $url, "url3" => $url],
                    'bindTypes' => [
                        "url1" => Column::BIND_PARAM_STR,
                        "url2" => Column::BIND_PARAM_STR,
                        "url3" => Column::BIND_PARAM_INT
                    ]
                ]
            )->getFirst();

        } elseif ($controller !== null) {
            $page = Page::find(
                [
                    'conditions' => 'controller=:controller:',
                    'bind' => ["controller" => $controller],
                    'bindTypes' => ["controller" => Column::BIND_PARAM_STR]
                ]
            )->getFirst();
        } elseif ($type !== null) {
            $page = Page::find(
                [
                    'conditions' => 'type=:type:',
                    'bind' => ["type" => $type],
                    'bindTypes' => ["type" => Column::BIND_PARAM_STR]
                ]
            )->getFirst();
        }


        if (!$page || !$page->isAllowed()) {
            return $this->dispatcher->forward(
                [
                    'controller' => 'Error',
                    'action' => 'show404'
                ]
            );
        }

        // Resort content by sides.
        $content = [];
        $renderer = Renderer::getInstance($this->getDI());
        foreach ($page->getWidgets() as $widget) {
            $content[$widget->layout][] = $renderer->renderWidgetId($widget->widget_id, $widget->getParams());
        }

        $this->view->content = $content;
        $this->view->page = $page;

        $this->view->pick('layouts/page');
    }

    /**
     * Disable header rendering.
     *
     * @return $this
     */
    public function disableHeader()
    {
        $this->view->disableHeader = true;

        return $this;
    }

    /**
     * Disable footer rendering.
     *
     * @return $this
     */
    public function disableFooter()
    {
        $this->view->disableFooter = true;

        return $this;
    }

    /**
     * Resolve modal window result.
     *
     * @param array $params Modal params.
     *
     * @return void
     */
    public function resolveModal(array $params = [])
    {
        if (empty($params)) {
            $params['hide'] = true;
        }

        $this->view->setVars($params, false);
        $this->view->hideSave = true;
        $this->view->pick('utils/modal');
    }

    /**
     * Setup assets.
     *
     * @return void
     */
    protected function _setupAssets()
    {
        $this->assets->set(
            AssetManager::DEFAULT_COLLECTION_CSS,
            $this->assets->getEmptyCssCollection()
                ->addCss('external/jquery/jquery-ui.css')
                ->addCss('assets/css/constants.css')
                ->addCss('assets/css/theme.css')
        );

        $this->assets->set(
            AssetManager::DEFAULT_COLLECTION_JS,
            $this->assets->getEmptyJsCollection()
                ->addJs('external/jquery/jquery-2.1.0.js')
                ->addJs('external/jquery/jquery-ui-1.10.4.js')
                ->addJs('external/jquery/jquery.cookie.js')
                ->addJs('assets/js/core/core.js')
                ->addJs('assets/js/core/i18n.js')
                ->addJs('assets/js/core/form.js')
                ->addJs('assets/js/core/form/remote-file.js')
                ->addJs('assets/js/core/widgets/grid.js')
                ->addJs('assets/js/core/widgets/autocomplete.js')
                ->addJs('assets/js/core/widgets/modal.js')
                ->addJs('assets/js/core/widgets/ckeditor.js')
        );

        if ($this->di->has('profiler')) {
            $this->di->get('assets')
                ->collection(AssetManager::DEFAULT_COLLECTION_CSS)
                ->addCss('assets/css/core/profiler.css');

            $this->di->get('assets')
                ->collection(AssetManager::DEFAULT_COLLECTION_JS)
                ->addCss('assets/js/core/profiler.js');
        }

        $this->addDefaultJsTranslations();
    }
}