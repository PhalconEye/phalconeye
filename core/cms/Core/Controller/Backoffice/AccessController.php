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

namespace Core\Controller\Backoffice;

use Core\Api\AclApi;
use Core\Form\CoreForm;
use Core\Grid\Backoffice\AccessGrid;
use Core\Model\AccessModel;
use Phalcon\Http\ResponseInterface;
use User\Model\RoleModel;

/**
 * Admin access controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/access")
 */
class AccessController extends AbstractBackofficeController
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="backoffice-access")
     */
    public function indexAction()
    {
        $grid = new AccessGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Edit access.
     *
     * @param int $id Identity.
     *
     * @return ResponseInterface|mixed|void
     *
     * @Route("/edit/{id:[a-zA-Z_-]+}", methods={"GET", "POST"}, name="backoffice-access-edit")
     */
    public function editAction($id)
    {
        // Normalize id.
        $urlId = $id;
        $id = str_replace('_', '\\', $id);

        // Check current role change request.
        $changeRole = $this->request->get('role');
        if ($changeRole !== null) {
            $this->session->set('backoffice-current-role', $changeRole);

            return $this->response->redirect(['for' => 'backoffice-access-edit', 'id' => $urlId]);
        }

        $resources = $this->core->acl()->getResources();
        $resourceFound = false;
        foreach ($resources as $resource) {
            if ($resource->getName() == $id) {
                $resourceFound = true;
                break;
            }
        }

        if (!$resourceFound) {
            return $this->response->redirect(['for' => 'backoffice-access']);
        }

        // get all roles and current
        $roles = RoleModel::find();
        $currentRole = $this->session->get('backoffice-current-role');
        $currentRole = RoleModel::findFirst($currentRole);
        if (!$currentRole) {
            $currentRole = RoleModel::getRoleByType(AclApi::DEFAULT_ROLE_ADMIN);
        }

        $objectAcl = $this->core->acl()->getObject($id);
        $form = $this->_getForm($objectAcl, $currentRole);

        $this->view->currentObject = $id;
        $this->view->form = $form;
        $this->view->roles = $roles;
        $this->view->currentRole = $currentRole;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $data = $form->getValues();
        // save actions
        foreach ($objectAcl->actions as $action) {
            $result = AccessModel::findFirst(
                [
                    "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                    "bind" => [
                        1 => $id,
                        2 => $action,
                        3 => $currentRole->id
                    ]
                ]
            );


            if (!$result) {
                $result = new AccessModel();
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
            $result = AccessModel::findFirst(
                [
                    "conditions" => "object = ?1 AND action = ?2 AND role_id = ?3",
                    "bind" => [
                        1 => $id,
                        2 => $options,
                        3 => $currentRole->id
                    ]
                ]
            );

            if (!$result) {
                $result = new AccessModel();
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
     * @param RoleModel $currentRole Role object.
     *
     * @return CoreForm
     */
    protected function _getForm($objectAcl, $currentRole)
    {
        $form = new CoreForm();

        if (!empty($objectAcl->actions)) {
            $form->addHtml('header_actions', '<h4>' . $this->di->get('i18n')->_('Actions') . '</h4>');
            foreach ($objectAcl->actions as $action) {
                $form->addCheckbox(
                    $action,
                    ucfirst($action),
                    sprintf(
                        'ACTION_%s_%s_DESCRIPTION',
                        strtoupper(str_replace('\\', '_', $objectAcl->name)),
                        strtoupper($action)
                    ),
                    1,
                    $this->core->acl()->isAllowed($currentRole->name, $objectAcl->name, $action),
                    0
                );
            }
        }

        if (!empty($objectAcl->options)) {
            $form->addHtml('header_options', '<br/><br/><h4>' . $this->di->get('i18n')->_('Options') . '</h4>');

            foreach ($objectAcl->options as $option) {
                $form->addText(
                    $option,
                    ucfirst($option),
                    sprintf(
                        'OPTION_%s_%s_DESCRIPTION',
                        strtoupper(str_replace('\\', '_', $objectAcl->name)),
                        strtoupper($option)
                    ),
                    $this->core->acl()->getAllowedValue($objectAcl->name, $currentRole, $option)
                );
            }
        }
        $form
            ->addFooterFieldSet()
            ->addButton('save')
            ->addButtonLink('cancel', 'Cancel', ['for' => 'backoffice-access']);
        return $form;
    }
}