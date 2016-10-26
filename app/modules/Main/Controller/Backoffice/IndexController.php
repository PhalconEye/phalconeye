<?php
namespace Main\Controller\Backoffice;

use Core\Controller\Backoffice\AbstractBackofficeController;

/**
 * Backoffice Index controller.
 *
 * @category PhalconEye\Module
 * @package  Controller
 *
 * @RoutePrefix("/backoffice/main")
 */
class IndexController extends AbstractBackofficeController
{
    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="backoffice-main")
     */
    public function indexAction()
    {

    }
}
