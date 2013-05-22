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

namespace Core\Controller;

/**
 * @RoutePrefix("/admin/languages", name="admin-languages")
 */
class AdminLanguagesController extends \Core\Controller\BaseAdmin
{
    CONST FLAGS_DIR = '/public/files/languages/';

    public function init()
    {
        $navigation = new \Engine\Navigation();
        $navigation
            ->setItems(array(
                'index' => array(
                    'href' => 'admin/languages',
                    'title' => 'Browse',
                    'prepend' => '<i class="icon-list icon-white"></i>'
                ),
                1 => array(
                    'href' => 'javascript:;',
                    'title' => '|'
                ),
                'create' => array(
                    'href' => 'admin/languages/create',
                    'title' => 'Create new language',
                    'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                )));

        $this->view->navigation = $navigation;

    }

    /**
     * @Get("/", name="admin-languages")
     */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $builder = $this->modelsManager->createBuilder()
            ->from('\Core\Model\Language');

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            array(
                "builder" => $builder,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->paginator = $page;
    }

    /**
     * @Route("/create", methods={"GET", "POST"}, name="admin-languages-create")
     */
    public function createAction()
    {
        $form = new \Core\Form\Admin\Language\Create();
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        if (!is_dir(ROOT_PATH . self::FLAGS_DIR)) {
            mkdir(ROOT_PATH . self::FLAGS_DIR, 766, true);
        }

        $files = $this->request->getUploadedFiles();
        $lang = $form->getValues();

        // upload flag
        if (count($files) == 1) {
            $iconPath = self::FLAGS_DIR . $lang->getLocale() . substr($files[0]->getName(), -4);
            @unlink(ROOT_PATH . $iconPath);
            $files[0]->moveTo(ROOT_PATH . $iconPath);
            $lang->setIcon($iconPath);
            $lang->save();
        }

        // check language file
        $file = ROOT_PATH . '/app/var/languages/' . $lang->getLocale() . '.php';
        if (!file_exists($file)) {
            file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . '$messages = array();');
        }

        $lang->save();
        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(array('for' => "admin-languages"));
    }

    /**
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit")
     */
    public function editAction($id)
    {
        $item = \Core\Model\Language::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => "admin-languages"));


        $form = new \Core\Form\Admin\Language\Edit($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $files = $this->request->getUploadedFiles();
        $lang = $form->getValues();

        if (count($files) == 1) {
            $iconPath = self::FLAGS_DIR . $lang->getLocale() . substr($files[0]->getName(), -4);
            @unlink(ROOT_PATH . $iconPath);
            $files[0]->moveTo(ROOT_PATH . $iconPath);
            $lang->setIcon(str_replace('/public/', '', $iconPath));
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
        $item = \Core\Model\Language::findFirst($id);
        if ($item) {
            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
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
        $item = \Core\Model\Language::findFirst($id);
        if (!$item)
            return $this->response->redirect(array('for' => "admin-languages"));

        $currentPage = $this->request->getQuery('page', 'int', 1);
        if ($currentPage < 1) $currentPage = 1;

        $search = $this->request->get('search');
        if ($search != null) {
            $builder = $this->modelsManager->createBuilder()
                ->from('\Core\Model\LanguageTranslation')
                ->where("\\Core\\Model\\LanguageTranslation.original LIKE '%{$search}%'")
                ->orWhere("\\Core\\Model\\LanguageTranslation.translated LIKE '%{$search}%'");
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->from('\Core\Model\LanguageTranslation');
        }

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(
            array(
                "builder" => $builder,
                "limit" => 25,
                "page" => $currentPage
            )
        );

        // Get the paginated results
        $page = $paginator->getPaginate();

        $this->view->paginator = $page;
        $this->view->search = $search;
        $this->view->lang =$item;

    }

    /**
     * @Route("/create-item", methods={"GET", "POST"}, name="admin-languages-create-item")
     */
    public function createItemAction()
    {
        $form = new \Core\Form\Admin\Language\CreateItem();
        $this->view->form = $form;

        $data = array(
            'language_id' => $this->request->get('language_id')
        );

        $form->setValues($data);

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $item = $form->getValues();

        $this->view->created = $item;
    }

    /**
     * @Route("/edit-item/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit-item")
     */
    public function editItemAction($id)
    {
        $item = \Core\Model\LanguageTranslation::findFirst($id);

        $form = new \Core\Form\Admin\Language\EditItem($item);
        $this->view->form = $form;

        $data = array(
            'language_id' => $this->request->get('language_id'),
        );

        $form->setValues($data);

        if (!$this->request->isPost() || !$form->isValid($_POST)) {
            return;
        }

        $this->view->edited = $form->getValues();
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
            if (!empty($search)) {
                return $this->response->redirect("admin/languages/manage/{$languageId}?search=" . $search);
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

            $languages = \Core\Model\Language::find();
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

