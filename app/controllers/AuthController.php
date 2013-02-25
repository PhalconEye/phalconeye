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
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

class AuthController extends Controller
{
    public function loginAction()
    {
        if (User::getViewer()->getId())
            $this->response->redirect()->send();

        $form = new Form_Auth_Login();

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            $this->view->setVar('form', $form);
            return;
        }

        $login = $this->request->getPost('login', 'string');
        $password = $this->request->getPost('password', 'string');

        $user = User::findFirst(array(
            "email = ?0 OR username = ?0",
            "bind" => array($login),
            "bindTypes" => array(\Phalcon\Db\Column::BIND_PARAM_STR)
        ));

        if ($user) {
            $userPassword = $user->getPassword();
            if ($this->security->checkHash($password, $userPassword)) {
                $this->auth->authenticate($user->getId());
                $this->response->redirect()->send();
            }
        }

        $form->addError('Login or password are incorrect!');
        $this->view->setVar('form', $form);

    }

    public function logoutAction()
    {
        if (User::getViewer()->getId())
            $this->auth->clearAuth();

        $this->response->redirect()->send();
    }

    public function registerAction()
    {
        if (User::getViewer()->getId())
            $this->response->redirect()->send();

        $form = new Form_Auth_Register();

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            $this->view->setVar('form', $form);
            return;
        }

        $password = $this->request->getPost('password', 'string');
        $repeatPassword = $this->request->getPost('password', 'string');
        if ($password != $repeatPassword) {
            $form->addError("Passwords doesn't match!");
            $this->view->setVar('form', $form);
            return;
        }

        $user = new User();
        $data = $form->getData();
        $data['password'] = $this->security->hash($data['password']);
        if (!$user->save($data)) {
            foreach ($user->getMessages() as $message) {
                $form->addError($message);
            }
            $this->view->setVar('form', $form);
            return;
        }

        $user->role_id = Role::getDefaultRole()->getId();
        $user->save();

        $this->auth->authenticate($user->getId());
        $this->response->redirect()->send();

    }
}

