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

namespace Core\Controller;

use Engine\Application;

/**
 * Error handler.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class ErrorController extends AbstractController
{
    /**
     * 404 page.
     *
     * @return void
     */
    public function show404Action()
    {
        $this->response->setStatusCode('404', 'Page not found');
        $this->view->pick('Error/show404', Application::CMS_MODULE_CORE);
    }

    /**
     * 500 page.
     *
     * @return void
     */
    public function show500Action()
    {
        $this->response->setStatusCode('500', 'Internal Server Error');
        $this->view->pick('Error/show500', Application::CMS_MODULE_CORE);
    }
}

