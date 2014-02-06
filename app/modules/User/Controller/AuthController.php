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
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace User\Controller;

use Core\Controller\AbstractController;
use Phalcon\Db\Column;
use Phalcon\Http\ResponseInterface;
use User\Form\Auth\Login as LoginForm;
use User\Form\Auth\Register as RegisterForm;
use User\Model\Role;
use User\Model\User;

/**
 * Auth handler.
 *
 * @category  PhalconEye
 * @package   User\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class AuthController extends AbstractController
{
    /**
     * Login action.
     *
     * @return mixed
     *
     * @Route("/login", methods={"GET", "POST"}, name="login")
     */
    public function loginAction()
    {
        if (User::getViewer()->id) {
            return $this->response->redirect();
        }

        $form = new LoginForm();
        if (!$this->request->isPost() || !$form->isValid()) {
            $this->view->form = $form;
            return;
        }

        $login = $this->request->getPost('login', 'string');
        $password = $this->request->getPost('password', 'string');

        $user = User::findFirst(
            [
                "email = ?0 OR username = ?0",
                "bind" => [$login],
                "bindTypes" => [Column::BIND_PARAM_STR]
            ]
        );

        if ($user) {
            if ($this->security->checkHash($password, $user->password)) {
                $this->core->auth()->authenticate($user->id);

                return $this->response->redirect();
            }
        }

        $form->addError('Incorrect email or password!');
        $this->view->form = $form;
    }

    /**
     * Logout action.
     *
     * @return ResponseInterface
     *
     * @Route("/logout", methods={"GET", "POST"}, name="logout")
     */
    public function logoutAction()
    {
        if (User::getViewer()->id) {
            $this->core->auth()->clearAuth();
        }

        return $this->response->redirect();
    }

    /**
     * Register action.
     *
     * @return mixed
     *
     * @Route("/register", methods={"GET", "POST"}, name="register")
     */
    public function registerAction()
    {
        if (User::getViewer()->id) {
            return $this->response->redirect();
        }

        $form = new RegisterForm();

        if (!$this->request->isPost() || !$form->isValid()) {
            $this->view->form = $form;

            return;
        }

        $password = $form->getValue('password');
        $repeatPassword = $form->getValue('repeatPassword');
        if ($password != $repeatPassword) {
            $form->addError("Passwords doesn't match!", 'password');
            $this->view->form = $form;

            return;
        }

        $user = new User();
        $data = $form->getValues();
        $user->role_id = Role::getDefaultRole()->id;
        if (!$user->save($data)) {
            foreach ($user->getMessages() as $message) {
                $form->addError($message);
            }
            $this->view->form = $form;

            return;
        }

        $this->core->auth()->authenticate($user->id);

        return $this->response->redirect();
    }
}