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

/**
 * @RoutePrefix("/admin/access")
 */
class AdminAccessController extends AdminController
{
    /**
     * @Route("/", methods={"GET"}, name="admin-access")
     */
    public function indexAction()
    {
        $resources = $this->acl->_()->getResources();
        $objects = array();

        $allActions = array();
        $allObjects = array();

        foreach ($resources as $resource) {
            $object = $this->acl->getObjectAcl($resource->getName());
            if ($object == null) continue;

            $allActions = array_merge($allActions, $object->actions, $object->options);
            $allObjects[] = $resource->getName();

            $object->actions = implode(', ', $object->actions);
            $object->options = implode(', ', $object->options);

            $objects[] = $object;

        }

        // cleanup
        // remove unused actions and options
        $this->modelsManager->executeQuery(
            "DELETE FROM Access WHERE action NOT IN ('" . implode("', '", $allActions) . "') OR object NOT IN ('" . implode("', '", $allObjects) . "')"
        );

        $this->view->setVar('objects', $objects);
    }

    /**
     * @Route("/edit/{id:[a-zA-Z0-9_]+}", methods={"GET", "POST"}, name="admin-access-edit")
     */
    public function editAction($id)
    {
        // check current role change request
        $changeRole = $this->request->get('role');
        if ($changeRole !== null) {
            $this->session->set('admin-current-role', $changeRole);
            return $this->response->redirect(array('for' => 'admin-access-edit', 'id' => $id));
        }

        $resources = $this->acl->_()->getResources();
        $resourceFound = false;
        foreach ($resources as $resource) {
            if ($resource->getName() == $id) {
                $resourceFound = true;
                break;
            }
        }

        if (!$resourceFound)
            return $this->response->redirect(array('for' => 'admin-access'));

        // get all roles and current
        $roles = Role::find();
        $currentRole = $this->session->get('admin-current-role');
        $currentRole = Role::findFirst($currentRole);
        if (!$currentRole) {
            $currentRole = Role::getRoleByType(Api_Acl::ROLE_TYPE_ADMIN);
        }

        $objectAcl = $this->acl->getObjectAcl($id);
        $form = new Form();

        if (!empty($objectAcl->actions)) {
            $form->addElement('html', 'header_actions',
                array(
                    'ignore' => true,
                    'html' => '<h4>' . $this->di->get('trans')->_('Actions') . '</h4>'
                ));

            foreach ($objectAcl->actions as $action) {
                $form->addElement('checkField', $action, array(
                    'label' => ucfirst($action),
                    'description' => sprintf('ACCESS_OBJECT_%s_ACTION_%s', strtoupper($objectAcl->name), strtoupper($action)),
                    'options' => 1,
                    'value' => $this->acl->_()->isAllowed($currentRole->getName(), $objectAcl->name, $action)
                ));
            }
        }

        if (!empty($objectAcl->options)) {
            $form->addElement('html', 'header_options',
                array(
                    'ignore' => true,
                    'html' => '<br/><br/><h4>' . $this->di->get('trans')->_('Options') . '</h4>'
                ));

            foreach ($objectAcl->options as $option) {
                $form->addElement('textField', $option, array(
                    'label' => ucfirst($option),
                    'description' => sprintf('ACCESS_OBJECT_%s_OPTION_%s', strtoupper($objectAcl->name), strtoupper($action)),
                    'value' => $this->acl->getAllowedValue($objectAcl->name, $currentRole, $option)
                ));
            }
        }
        $form->addButton('Save', true);

        $this->view->setVar('currentObject', $id);
        $this->view->setVar('form', $form);
        $this->view->setVar('roles', $roles);
        $this->view->setVar('currentRole', $currentRole);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $data = $form->getData();
        // save actions
        foreach ($objectAcl->actions as $action) {
            $result = Access::findFirst(array(
                "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                "bind" => array(
                    1 => $id,
                    2 => $action,
                    3 => $currentRole->getId()
                )
            ));


            if (!$result) {
                $result = new Access();
                $result->object = $id;
                $result->action = $action;
                $result->role_id = $currentRole->getId();
            }

            if (empty($data[$action])) {
                $result->value = 'deny';
            } else {
                $result->value = 'allow';
            }
            $result->save();
        }

        //save options
        foreach ($objectAcl->options as $options) {
            $result = Access::findFirst(array(
                "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                "bind" => array(
                    1 => $id,
                    2 => $options,
                    3 => $currentRole->getId()
                )
            ));


            if (!$result) {
                $result = new Access();
                $result->object = $id;
                $result->action = $options;
                $result->role_id = $currentRole->getId();
            }

            if (empty($data[$options]))
                $data[$options] = null;
            $result->value = $data[$options];
            $result->save();
        }

        $this->flash->success('Settings saved!');
    }
}