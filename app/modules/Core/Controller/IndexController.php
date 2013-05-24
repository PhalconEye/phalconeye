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

use Engine\Error;
use Engine\Package\Exception;

/**
 * @RoutePrefix("/", name="home")
 */
class IndexController extends \Core\Controller\Base
{
    /**
     * @Route("/", methods={"GET"}, name="home")
     */
    public function indexAction()
    {
        // check lang flag
        $locale = $this->request->get('lang', 'string', 'en');
        $this->session->set('locale', $locale);

        $this->renderContent(null, null, 'home');
    }


}

