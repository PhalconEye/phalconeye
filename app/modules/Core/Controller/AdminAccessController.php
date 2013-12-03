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

use Core\Api\Acl;
use Core\Model\Access;
use Engine\Form;
use Phalcon\Http\ResponseInterface;
use User\Model\Role;

/**
 * Admin access controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/access")
 */
class AdminAccessController extends AdminControllerBase
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="admin-access")
     */
    public function indexAction()
    {
        $resources = $this->core->acl()->_()->getResources();
        $objects = array();

        $allActions = array();
        $allObjects = array();

        foreach ($resources as $resource) {

            $object = $this->core->acl()->getObjectAcl($resource->getName());
            if ($object == null) {
                continue;
            }

            $allActions = array_merge($allActions, $object->actions, $object->options);
            $allObjects[] = $resource->getName();

            $object->actions = implode(', ', $object->actions);
            $object->options = implode(', ', $object->options);

            $objects[] = $object;

        }

        // Cleanup.
        // Remove unused actions and options.
        $this->modelsManager->executeQuery(
            "DELETE FROM Core\\Model\\Access WHERE action NOT IN ('" .
            implode("', '", $allActions) .
            "') OR object NOT IN ('" .
            implode("', '", $allObjects) . "')"
        );

        $this->view->objects = $objects;
    }

    /**
     * Edit access.
     *
     * @return ResponseInterface|mixed|void
     *
     * @Route("/edit", methods={"GET", "POST"}, name="admin-access-edit")
     */
    public function editAction()
    {
        $id = $this->request->get('id');
        // Check current role change request.
        $changeRole = $this->request->get('role');
        if ($changeRole !== null) {
            $this->session->set('admin-current-role', $changeRole);

            return $this->response->redirect('admin/access/edit?id=' . $id);
        }

        $resources = $this->core->acl()->_()->getResources();
        $resourceFound = false;
        foreach ($resources as $resource) {
            if ($resource->getName() == $id) {
                $resourceFound = true;
                break;
            }
        }

        if (!$resourceFound) {
            return $this->response->redirect(array('for' => 'admin-access'));
        }

        // get all roles and current
        $roles = Role::find();
        $currentRole = $this->session->get('admin-current-role');
        $currentRole = Role::findFirst($currentRole);
        if (!$currentRole) {
            $currentRole = Role::getRoleByType(Acl::DEFAULT_ROLE_ADMIN);
        }

        $objectAcl = $this->core->acl()->getObjectAcl($id);
        $form = $this->_getForm($objectAcl, $changeRole);

        $this->view->currentObject = $id;
        $this->view->form = $form;
        $this->view->roles = $roles;
        $this->view->currentRole = $currentRole;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $data = $form->getValues();
        // save actions
        foreach ($objectAcl->actions as $action) {
            $result = Access::findFirst(array(
                "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                "bind" => array(
                    1 => $id,
                    2 => $action,
                    3 => $currentRole->id
                )
            ));


            if (!$result) {
                $result = new Access();
                $result->object = $id;
                $result->action = $action;
                $result->role_id = $currentRole->id;
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
                    3 => $currentRole->id
                )
            ));

            if (!$result) {
                $result = new Access();
                $result->object = $id;
                $result->action = $options;
                $result->role_id = $currentRole->id;
            }

            if (empty($data[$options])) {
                $data[$options] = null;
            }

            $result->value = $data[$options];
            $result->save();
        }
        $this->core->acl()->clearAcl();
        $this->flash->success('Settings saved!');
    }

    /**
     * Get access editing form.
     *
     * @param \stdClass $objectAcl   Acl object with data.
     * @param Role      $currentRole Role object.
     *
     * @return Form
     */
    protected function _getForm($objectAcl, $currentRole)
    {
        $form = new Form();

        if (!empty($objectAcl->actions)) {
            $form->addElement('html', 'header_actions',
                array(
                    'ignore' => true,
                    'html' => '<h4>' . $this->di->get('trans')->_('Actions') . '</h4>'
                ));

            foreach ($objectAcl->actions as $action) {
                $form->addElement('check', $action, array(
                    'label' => ucfirst($action),
                    'description' => sprintf(
                        'ACCESS_OBJECT_%s_ACTION_%s',
                        strtoupper($objectAcl->name),
                        strtoupper($action)
                    ),
                    'options' => 1,
                    'value' => $this->core->acl()->_()->isAllowed($currentRole->name, $objectAcl->name, $action)
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
                $form->addElement('text', $option, array(
                    'label' => ucfirst($option),
                    'description' => sprintf(
                        'ACCESS_OBJECT_%s_OPTION_%s',
                        strtoupper($objectAcl->name),
                        strtoupper($action)
                    ),
                    'value' => $this->core->acl()->getAllowedValue($objectAcl->name, $currentRole, $option)
                ));
            }
        }
        $form->addButton('Save', true);
    }
}