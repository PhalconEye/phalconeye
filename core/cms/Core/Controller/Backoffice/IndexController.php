<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller\Backoffice;

/**
 * Admin Index controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice")
 */
class IndexController extends AbstractBackofficeController
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Get("/", name="backoffice-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->debug = $this->config->application->debug;
        $this->view->profiler = $this->config->application->profiler;
    }

    /**
     * Action for mode changing.
     *
     * @return void
     *
     * @Get("/mode", name="backoffice-mode")
     */
    public function modeAction()
    {
        $this->view->disable();
        $this->config->application->debug = (bool)$this->request->get('flag', null, true);
        $this->config->save();
        $this->_clearCache();
    }

    /**
     * Action for profiler changing.
     *
     * @return void
     *
     * @Get("/profiler", name="backoffice-profiler")
     */
    public function profilerAction()
    {
        $this->view->disable();
        $this->config->application->profiler = (bool)$this->request->get('flag', null, true);
        $this->config->save();
        $this->_clearCache();
    }

    /**
     * Action for cleaning cache.
     *
     * @return void
     *
     * @Get("/clear", name="backoffice-clear")
     */
    public function cleanAction()
    {
        $this->view->disable();
        $this->_clearCache();
        $this->flashSession->success('Cache cleared!');
        $this->response->redirect(['for' => 'backoffice-home']);
    }
}

