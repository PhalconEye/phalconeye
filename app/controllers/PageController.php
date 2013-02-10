<?php

class PageController extends Controller
{
    public function indexAction($url)
    {
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

        // get footer and header
        // @todo: cache them?
        $header = Page::findFirst("type = 'header'");
        $footer = Page::findFirst("type = 'footer'");

        $this->view->setVar('content', $content);
        $this->view->setVar('header', $header->getWidgets());
        $this->view->setVar('footer', $footer->getWidgets());

    }
}

