<?php

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

        $this->auth->authenticate($user->getId());
        $this->response->redirect()->send();

    }
}

