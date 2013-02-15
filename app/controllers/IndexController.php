<?php

class IndexController extends Controller
{
    public function indexAction()
    {
        // check lang flag
        $locale = $this->request->get('lang', 'string', 'en');
        $this->session->set('locale', $locale);

        $this->renderContent(null, null, 'home');
    }

}

