<?php

class PageController extends Controller
{
    public function indexAction($url)
    {
        $this->renderContent($url);
    }
}

