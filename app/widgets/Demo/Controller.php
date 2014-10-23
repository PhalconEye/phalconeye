<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Widget\Demo;

use Engine\Widget\Controller as WidgetController;

/**
 * Demo widget controller.
 *
 * @category  PhalconEye
 * @package   Widget\Demo
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Controller extends WidgetController
{

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $form = $this->view->form = new DemoForm();

        if ($this->request->isPost()) {

            // Intentionally remove a required value to get some error messages
            $post = $this->request->getPost();
            $post['robot_name'] = '';

            $form->isValid($post);
        }
    }
}