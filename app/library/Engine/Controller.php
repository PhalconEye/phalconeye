<?php

/**
 * @property \Phalcon\Db\Adapter\Pdo $db
 * @property \Phalcon\Cache\Backend $cacheData
 * @property Api_Acl $acl
 */
class Controller extends \Phalcon\Mvc\Controller
{

    /**
     * Initializes the controller
     */
    public function initialize()
    {
        // run init function
        if (method_exists($this, 'init'))
            $this->init();
    }

    public function renderContent($url = null, $controller = null, $type = null)
    {
        $page = null;
        if ($url !== null) {
            $page = Page::find(array(
                'conditions' => 'url=:url1: OR url=:url2: OR id = :url3:',
                'bind' => (array(
                    "url1" => $url,
                    "url2" => '/' . $url,
                    "url3" => $url
                )),
                'bindTypes' => (array(
                    "url1" => \Phalcon\Db\Column::BIND_PARAM_STR,
                    "url2" => \Phalcon\Db\Column::BIND_PARAM_STR,
                    "url3" => \Phalcon\Db\Column::BIND_PARAM_INT
                ))
            ))->getFirst();

        } elseif ($controller !== null) {
            $page = Page::find(array(
                'conditions' => 'controller=:controller:',
                'bind' => (array(
                    "controller" => $controller
                )),
                'bindTypes' => (array(
                    "controller" => \Phalcon\Db\Column::BIND_PARAM_STR
                ))
            ))->getFirst();
        }
        elseif($type !== null){
            $page = Page::find(array(
                'conditions' => 'type=:type:',
                'bind' => (array(
                    "type" => $type
                )),
                'bindTypes' => (array(
                    "type" => \Phalcon\Db\Column::BIND_PARAM_STR
                ))
            ))->getFirst();
        }


        if (!$page) {
            return $this->dispatcher->forward(array(
                'controller' => 'error',
                'action' => 'show404'
            ));
        }

        // resort content by sides
        $content = array();
        foreach ($page->getWidgets() as $widget) {
            $content[$widget->getLayout()][] = $widget;
        }

        $this->view->setVar('content', $content);
        $this->view->setVar('page', $page);


        $this->view->pick('layouts/page');

    }

}