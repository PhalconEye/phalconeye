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

namespace User\Controller;

class AuthController extends \Core\Controller\Base
{
    /**
     * @Route("/login", methods={"GET", "POST"}, name="login")
     */
    public function loginAction()
    {
        if (\User\Model\User::getViewer()->getId()) {
            $this->response->redirect()->send();
        }

        $form = new \User\Form\Auth\Login();

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        $login = $this->request->getPost('login', 'string');
        $password = $this->request->getPost('password', 'string');

        $user = \User\Model\User::findFirst(array(
            "email = ?0 OR username = ?0",
            "bind" => array($login),
            "bindTypes" => array(\Phalcon\Db\Column::BIND_PARAM_STR)
        ));

        if ($user) {
            $userPassword = $user->getPassword();
            if ($this->security->checkHash($password, $userPassword)) {
                $this->core->auth()->authenticate($user->getId());
                return $this->response->redirect()->send();
            }
        }

        $form->addError('Email or password are incorrect!');
        $this->view->form = $form;

    }

    /**
     * @Route("/logout", methods={"GET", "POST"}, name="logout")
     */
    public function logoutAction()
    {
        if (\User\Model\User::getViewer()->getId())
            $this->core->auth()->clearAuth();

        $this->response->redirect()->send();
    }

    /**
     * @Route("/register", methods={"GET", "POST"}, name="register")
     */
    public function registerAction()
    {
        if (\User\Model\User::getViewer()->getId()) {
            $this->response->redirect()->send();
        }

        $form = new \User\Form\Auth\Register();

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        $password = $this->request->getPost('password', 'string');
        $repeatPassword = $this->request->getPost('password', 'string');
        if ($password != $repeatPassword) {
            $form->addError("Passwords doesn't match!");
            $this->view->form = $form;
            return;
        }

        $user = new \Core\Model\User();
        $data = $form->getValues();
        $data['password'] = $this->security->hash($data['password']);
        if (!$user->save($data)) {
            foreach ($user->getMessages() as $message) {
                $form->addError($message);
            }
            $this->view->form = $form;
            return;
        }

        $user->role_id = \User\Model\Role::getDefaultRole()->getId();
        $user->save();

        $this->auth->authenticate($user->getId());
        $this->response->redirect()->send();

    }
}

