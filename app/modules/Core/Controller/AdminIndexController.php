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

/**
 * @RoutePrefix("/admin")
 */
class AdminIndexController extends \Core\Controller\BaseAdmin
{
    /**
     * @Get("/", name="admin-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->debug = $this->config->application->debug;
    }

    /**
     * @Get("/mode", name="admin-mode")
     */
    public function modeAction()
    {
        $this->view->disable();

        $this->config->application->debug = (bool)$this->request->get('debug', null, true);

        $this->app->saveConfig();
        $this->app->clearCache();
     }
}

