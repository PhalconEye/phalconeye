<?php
namespace %nameUpper%\Controller\Backoffice;

use %defaultModuleUpper%\Controller\Backoffice\AbstractBackofficeController;

/**
 * Backoffice Index controller.
 *
 * @category PhalconEye\Module
 * @package  Controller
 *
 * @RoutePrefix("/backoffice/%name%")
 */
class IndexController extends AbstractBackofficeController
{
    /**
     * Module index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="backoffice-%name%")
     */
    public function indexAction()
    {

    }
}
