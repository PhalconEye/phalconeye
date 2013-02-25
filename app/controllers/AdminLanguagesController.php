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

class AdminLanguagesController extends Controller
{
    CONST FLAGS_DIR = '/public/img/phalconeye/languages/';

    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItemPrependContent('<i class="icon-chevron-right"></i> ')
            ->setListClass('nav nav-list admin-sidenav')
            ->setItems(array(
            'index' => array(
                'href' => 'admin/languages',
                'title' => 'Browse'
            ),
            'create' => array(
                'href' => 'admin/languages/create',
                'title' => 'Create new language'
            )))
            ->setActiveItem($this->dispatcher->getActionName());

        $this->view->setVar('navigation', $navigation);

    }

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

        $this->response->redirect("admin/languages");
    }

    public function editAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/languages");


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

        $this->response->redirect("admin/languages");
    }

    public function deleteAction($id)
    {
        $item = Language::findFirst($id);
        if ($item)
            $item->delete();

        return $this->response->redirect("admin/languages");
    }

    public function manageAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item)
            return $this->response->redirect("admin/languages");

        $translations = $item->getLanguageTranslation();

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

        $this->view->setVar('lang', $item);

    }

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

    public function editItemAction()
    {
        $id = $this->request->get('id', 'int');
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

    public function deleteItemAction($id)
    {
        $item = LanguageTranslation::findFirst($id);
        if ($item)
            $item->delete();

        $languageId = $this->request->get('lang');

        if ($languageId) {
            return $this->response->redirect("admin/languages/manage/{$languageId}");
        }

        return $this->response->redirect("admin/languages");
    }

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

        return $this->response->redirect("admin/languages");
    }
}

