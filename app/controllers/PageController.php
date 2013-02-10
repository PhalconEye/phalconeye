<?php

class PageController extends Controller
{
    public function indexAction($url)
    {
        $page = Page::query()
            ->where('url=:url: OR url=:url1: OR id = :url:')
            ->bind(array(
            "url" => $url,
            "url1" => '/' . $url
        ))
            ->limit(1)
            ->execute()
            ->getFirst();

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
        $this->view->setVar('header', array());
        $this->view->setVar('footer', array());

    }
}

