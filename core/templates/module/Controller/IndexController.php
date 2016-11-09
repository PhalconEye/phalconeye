<?php
namespace %name%\Controller;

use %defaultModule%\Controller\AbstractController;

/**
 * Index controller.
 *
 * @category PhalconEye\Module
 * @package  Controller
 *
 * @RoutePrefix("/%name%")
 */
class IndexController extends AbstractController
{
    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="%name%")
     */
    public function indexAction()
    {

    }
}
