<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller;

/**
 * Admin Index controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin")
 */
class AdminIndexController extends BaseAdmin
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Get("/", name="admin-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->debug = $this->config->application->debug;
    }

    /**
     * Action for mode changing.
     *
     * @return void
     *
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

