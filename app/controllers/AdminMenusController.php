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

class AdminMenusController extends Controller
{
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'index' => array(
                'href' => 'admin/menus',
                'title' => 'Browse'
            ),
            'create' => array(
                'href' => 'admin/menus/create',
                'title' => 'Create new menu'
            )))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigation', $navigation);
    }

    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $items = Menu::find();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $items,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
    }

    public function createAction()
    {
        $form = new Form_Admin_Menus_Create();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $this->response->redirect("admin/menus/manage/" . $form->getData()->getId());
    }

    public function editAction($id)
    {
        $item = Menu::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/menus");


        $form = new Form_Admin_Menus_Edit($item);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $this->response->redirect("admin/menus");
    }

    public function deleteAction($id)
    {
        $item = Menu::findFirst($id);
        if ($item)
            $item->delete();

        return $this->response->redirect("admin/menus");
    }

    public function manageAction($id)
    {
        $item = Menu::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/menus");

        $parentId = $this->request->get('parent_id', 'int');
        if ($parentId){
            $parent = MenuItem::findFirst($parentId);

            // get all parents
            $flag = true;
            $parents = array();
            $parents[] = $currentParent = $parent;
            while($flag){
                if ($currentParent->getParentId()){
                    $parents[] = $currentParent = $currentParent->getParent();
                }
                else{
                    $flag = false;
                }
            }
            $parents = array_reverse($parents);

            $this->view->setVar('parent', $parent);
            $this->view->setVar('parents', $parents);
            $this->view->setVar('items', $parent->getMenuItem(array('order'=>'item_order ASC')));
        }
        else{
            $this->view->setVar('items', $item->getMenuItem(array(
                'parent_id IS NULL',
                'order'=>'item_order ASC'
            )));
        }

        $this->view->setVar('menu', $item);

    }

    public function createItemAction()
    {
        $form = new Form_Admin_Menus_CreateItem();
        $this->view->setVar('form', $form);

        $data = array(
            'menu_id' => $this->request->get('menu_id'),
            'parent_id' => $this->request->get('parent_id')
        );

        $form->setData($data);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $urlType = $this->request->getPost('url_type', 'int', 0);
        $item = $form->getData();

        // clear url type
        if ($urlType == 0) {
            $item->setPageId(null);
        } else {
            $item->setUrl(null);
        }

        // set proper order
        $orderData = array(
            "menu_id = {$data['menu_id']}",
            'order' => 'item_order DESC'
        );
        if (!empty($data['parent_id'])){
            $orderData[0] .= " AND parent_id = {$data['parent_id']}";
        }
        $orderItem = MenuItem::findFirst($orderData);

        if ($orderItem->getId() != $item->getId())
            $item->setItemOrder($orderItem->getItemOrder() + 1);
        $item->save();

        $this->view->setVar('created', $item);
    }

    public function editItemAction()
    {
        $id = $this->request->get('id', 'int');
        $item = MenuItem::findFirst($id);

        $form = new Form_Admin_Menus_EditItem($item);
        $this->view->setVar('form', $form);

        $data = array(
            'menu_id' => $this->request->get('menu_id'),
            'parent_id' => $this->request->get('parent_id'),
            'url_type' => ($item->getPageId() == null ? 0 : 1),
        );

        if ($item->getPageId()){
            $page = Page::findFirst($item->getPageId());
            if ($page){
                $data['page_id'] = $page->getId();
                $data['page'] = $page->getTitle();
            }
        }


        $form->setData($data);


        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();

        // clear url type
        $urlType = $this->request->getPost('url_type', 'int', 0);
        if ($urlType == 0) {
            $item->setPageId(null);
        } else {
            $item->setUrl(null);
        }
        $item->save();

        $this->view->setVar('edited', $form->getData());
    }


    public function deleteItemAction($id)
    {
        $item = MenuItem::findFirst($id);
        $menuId = null;
        if ($item) {
            $menuId = $item->getMenuId();
            $item->delete();
        }

        $parentId = $this->request->get('parent_id');
        $parentLink = '';
        if ($parentId){
            $parentLink = "?parent_id={$parentId}";
        }
        if ($menuId)
            return $this->response->redirect("admin/menus/manage/{$menuId}{$parentLink}");

        return $this->response->redirect("admin/menus");
    }

    public function orderItemAction()
    {
        $order = $this->request->get('order', null, array());
        foreach($order as $index => $id){
            $this->db->update(MenuItem::getSourceStatic(), array('item_order'), array($index), "id = {$id}");
        }
        $this->view->disable();
    }

    public function suggestAction(){
        $this->view->disable();
        $query = $this->request->get('query');
        if (!$query){
            $this->response->setContent('[]')->send();
            return;
        }


        $results = Menu::find(
            array(
                "conditions" => "name LIKE ?1",
                "bind"       => array(1 => '%'.$query.'%')
            )
        );

        $data = array();
        foreach($results as $result){
            $data[] = array(
                'id' => $result->getId(),
                'label' => $result->getName()
            );
        }

        $this->response->setContent(json_encode($data))->send();
    }

}

