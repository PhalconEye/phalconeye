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

namespace Core\Controller;

use Phalcon\Mvc\Controller as PhController;
use Phalcon\Mvc\View as PhView;
use \Phalcon\Db\Column as PhDbColumn;

/**
 * @property \Phalcon\Db\Adapter\Pdo $db
 * @property \Phalcon\Cache\Backend  $cacheData
 * @property \Engine\Application     $app
 * @property \Engine\Asset\Manager   $assets
 * @property \Phalcon\Config         $config
 */
class Base extends PhController
{
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        if ($this->config->application->debug && $this->di->has('profiler')) {
            $this->profiler->start();
        }

        $this->view->setRenderLevel(PhView::LEVEL_ACTION_VIEW);
        $this->view->setPartialsDir('../../Core/View/partials/');

        $this->assets->get('css')
            ->addCss('assets/css/constants.css')
            ->addCss('assets/css/theme.css');

        $this->assets->get('js')
            ->addJs('assets/js/core/jquery.js')
            ->addJs('assets/js/core/jquery-ui.js')
            ->addJs('assets/js/core/core.js')
            ->addJs('assets/js/core/i18n.js')
            ->addJs('assets/js/core/autocomplete.js')
            ->addJs('assets/js/core/modal.js');

        if ($this->config->application->debug && $this->di->has('profiler')) {
            $this->di->get('assets')
                ->collection('css')
                ->addCss('assets/css/core/profiler.css');;

            $this->di->get('assets')
                ->collection('js')
                ->addCss('assets/js/core/profiler.js');;
        }

        // run init function
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }


    public function afterExecuteRoute()
    {
        if ($this->config->application->debug && $this->di->has('profiler')) {
            $this->profiler->stop(get_called_class(), 'controller');
        }
    }

    public function renderContent($url = null, $controller = null, $type = null)
    {
        $page = null;
        if ($url !== null) {
            $page = \Core\Model\Page::find(array(
                'conditions' => 'url=:url1: OR url=:url2: OR id = :url3:',
                'bind' => (array(
                        "url1" => $url,
                        "url2" => '/' . $url,
                        "url3" => $url
                    )),
                'bindTypes' => (array(
                        "url1" => PhDbColumn::BIND_PARAM_STR,
                        "url2" => PhDbColumn::BIND_PARAM_STR,
                        "url3" => PhDbColumn::BIND_PARAM_INT
                    ))
            ))->getFirst();

        } elseif ($controller !== null) {
            $page = \Core\Model\Page::find(array(
                'conditions' => 'controller=:controller:',
                'bind' => (array(
                        "controller" => $controller
                    )),
                'bindTypes' => (array(
                        "controller" => PhDbColumn::BIND_PARAM_STR
                    ))
            ))->getFirst();
        } elseif ($type !== null) {
            $page = \Core\Model\Page::find(array(
                'conditions' => 'type=:type:',
                'bind' => (array(
                        "type" => $type
                    )),
                'bindTypes' => (array(
                        "type" => PhDbColumn::BIND_PARAM_STR
                    ))
            ))->getFirst();
        }


        if (!$page || !$page->isAllowed()) {
            return $this->dispatcher->forward(array(
                'controller' => 'error',
                'action' => 'show404'
            ));
        }

        // increment views
//        $page->incrementViews();

        // resort content by sides
        $content = array();
        foreach ($page->getWidgets() as $widget) {
            $content[$widget->layout][] = $widget;
        }

        $this->view->content = $content;
        $this->view->page = $page;


        $this->view->pick('layouts/page');

    }

    public function disableHeader()
    {
        $this->view->disableHeader = true;
    }

    public function disableFooter()
    {
        $this->view->disableFooter = true;
    }

}