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
 * @RoutePrefix("/admin/languages", name="admin-languages")
 */
class AdminLanguagesController extends AdminController
{
    CONST FLAGS_DIR = '/public/img/phalconeye/languages/';

    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/languages',
                    'title' => 'Browse'
                ),
                1 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'create' => array(
                    'href' => 'admin/languages/create',
                    'title' => 'Create new language'
                )));

        $this->view->setVar('navigation', $navigation);

    }

    /**
     * @Get("/", name="admin-languages")
     */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $items = Language::find();

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

    /**
     * @Route("/create", methods={"GET", "POST"}, name="admin-languages-create")
     */
    public function createAction()
    {
        $form = new Form_Admin_Languages_Create();
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        if (!is_dir(ROOT_PATH . self::FLAGS_DIR)) {
            mkdir(ROOT_PATH . self::FLAGS_DIR, 766, true);
        }

        $files = $form->getFiles();
        $lang = $form->getData();

        // upload flag
        if (count($files) == 1) {
            $iconPath = self::FLAGS_DIR . $lang->getLocale() . substr($files[0]['name'], -4);
            @unlink(ROOT_PATH . $iconPath);
            move_uploaded_file($files[0]['tmp_name'], ROOT_PATH . $iconPath);
            $lang->setIcon($iconPath);
            $lang->save();
        }

        // check language file
        $file = ROOT_PATH . '/app/var/languages/' . $lang->getLocale() . '.php';
        if (!file_exists($file)) {
            file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . '$messages = array();');
        }

        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(array('for' => "admin-languages"));
    }

    /**
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit")
     */
    public function editAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => "admin-languages"));


        $form = new Form_Admin_Languages_Edit($item);
        $this->view->setVar('form', $form);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $files = $form->getFiles();
        $lang = $form->getData();

        if (count($files) == 1) {
            $iconPath = self::FLAGS_DIR . $lang->getLocale() . substr($files[0]['name'], -4);
            @unlink(ROOT_PATH . $iconPath);
            move_uploaded_file($files[0]['tmp_name'], ROOT_PATH . $iconPath);
            $lang->setIcon($iconPath);
            $lang->save();
        }


        $this->flashSession->success('Object saved!');
        return $this->response->redirect(array('for' => "admin-languages"));
    }

    /**
     * @Get("/delete/{id:[0-9]+}", name="admin-languages-delete")
     */
    public function deleteAction($id)
    {
        $item = Language::findFirst($id);
        if ($item){
            if ($item->delete()){
                $this->flashSession->notice('Object deleted!');
            }
            else{
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(array('for' => "admin-languages"));
    }

    /**
     * @Get("/manage/{id:[0-9]+}", name="admin-languages-manage")
     */
    public function manageAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => "admin-languages"));

        $search = $this->request->get('search');
        $options = array();
        if ($search != null) {
            $options = array("original LIKE '%{$search}%' OR translated LIKE '%{$search}%'");
        }

        $translations = $item->getLanguageTranslation($options);

        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $translations,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->setVar('paginator', $page);
        $this->view->setVar('search', $search);
        $this->view->setVar('lang', $item);

    }

    /**
     * @Route("/create-item", methods={"GET", "POST"}, name="admin-languages-create-item")
     */
    public function createItemAction()
    {
        $form = new Form_Admin_Languages_CreateItem();
        $this->view->setVar('form', $form);

        $data = array(
            'language_id' => $this->request->get('language_id')
        );

        $form->setData($data);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $item = $form->getData();

        $this->view->setVar('created', $item);
    }

    /**
     * @Route("/edit-item/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit-item")
     */
    public function editItemAction($id)
    {
        $item = LanguageTranslation::findFirst($id);

        $form = new Form_Admin_Languages_EditItem($item);
        $this->view->setVar('form', $form);

        $data = array(
            'language_id' => $this->request->get('language_id'),
        );

        $form->setData($data);

        if (!$this->request->isPost() || !$form->isValid($this->request)) {
            return;
        }

        $this->view->setVar('edited', $form->getData());
    }

    /**
     * @Get("/delete-item/{id:[0-9]+}", name="admin-languages-delete-item")
     */
    public function deleteItemAction($id)
    {
        $item = LanguageTranslation::findFirst($id);
        if ($item)
            $item->delete();

        $languageId = $this->request->get('lang');
        $search = $this->request->get('search');

        if ($languageId) {
            if (!empty($search)){
                return $this->response->redirect("admin/languages/manage/{$languageId}?search=".$search);
            }
            return $this->response->redirect(array('for' => "admin-languages-manage", 'id' => $languageId));
        }

        return $this->response->redirect(array('for' => "admin-languages"));
    }

    /**
     * @Get("/compile", name="admin-languages-compile")
     */
    public function compileAction()
    {
        // Prepare languages
        // Dump all data from database to *.po files

        try {

            $languages = Language::find();
            foreach ($languages as $language) {
                $language->generatePHP();
            }

            $this->flashSession->success($this->trans->_('Languages compilation finished!'));
        } catch (Exception $e) {
            $this->flashSession->error($this->trans->_('Compilation failed, error: <br/>' . $e->getMessage()));
        }

        return $this->response->redirect(array('for' => 'admin-languages'));
    }
}

