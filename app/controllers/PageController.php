<?php

class PageController extends Controller
{
    public function indexAction($url)
    {
        $page = Page::findFirst("url='{$url}' OR url='/{$url}'");

        if (!$page){
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

