<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
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

use Core\Controller\Grid\Admin\LanguageGrid;
use Core\Controller\Grid\Admin\LanguageTranslationGrid;
use Core\Form\Admin\Language\Create;
use Core\Form\Admin\Language\CreateItem;
use Core\Form\Admin\Language\Edit;
use Core\Form\Admin\Language\EditItem;
use Core\Form\FileForm;
use Core\Model\Language;
use Core\Model\LanguageTranslation;
use Engine\Config;
use Engine\Exception;
use Engine\Navigation;
use Phalcon\Http\ResponseInterface;

/**
 * Admin languages controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/admin/languages", name="admin-languages")
 */
class AdminLanguagesController extends AbstractAdminController
{
    /**
     * Init controller.
     *
     * @return void
     */
    public function init()
    {
        $navigation = new Navigation();
        $navigation
            ->setItems(
                [
                    'index' => [
                        'href' => 'admin/languages',
                        'title' => 'Browse',
                        'prepend' => '<i class="icon-list icon-white"></i>'
                    ],
                    1 => [
                        'href' => 'javascript:;',
                        'title' => '|'
                    ],
                    'create' => [
                        'href' => 'admin/languages/create',
                        'title' => 'Create new language',
                        'prepend' => '<i class="icon-plus-sign icon-white"></i>'
                    ]
                ]
            );

        $this->view->navigation = $navigation;
    }

    /**
     * Index action.
     *
     * @return void
     *
     * @Get("/", name="admin-languages")
     */
    public function indexAction()
    {
        $grid = new LanguageGrid($this->view);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Create language action.
     *
     * @return void|ResponseInterface
     *
     * @Route("/create", methods={"GET", "POST"}, name="admin-languages-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new Create();

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        /** @var Language $language */
        $language = $form->getEntity();

        // Check uploaded files.
        $this->_setLanguageIcon($language, $form);

        // Check language file.
        $file = $language->getCacheLocation();
        if (!file_exists($file)) {
            file_put_contents($file, '<?php' . PHP_EOL . PHP_EOL . '$messages = [];');
        }

        $language->save();
        $this->flashSession->success('New object created successfully!');
        return $this->response->redirect(['for' => "admin-languages"]);
    }

    /**
     * Edit language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit")
     */
    public function editAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => "admin-languages"]);
        }

        $form = new Edit($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        /** @var Language $language */
        $language = $form->getEntity();

        // Check uploaded files.
        $this->_setLanguageIcon($language, $form);

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => "admin-languages"]);
    }

    /**
     * Delete language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/delete/{id:[0-9]+}", name="admin-languages-delete")
     */
    public function deleteAction($id)
    {
        $item = Language::findFirst($id);
        if ($item) {
            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(['for' => "admin-languages"]);
    }

    /**
     * Manage language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/manage/{id:[0-9]+}", name="admin-languages-manage")
     */
    public function manageAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->trans->_('Language not found!'));
            return $this->response->redirect(['for' => "admin-languages"]);
        }

        $this->view->search = $this->request->get('search');
        $this->view->lang = $item;
        $grid = new LanguageTranslationGrid($this->view, $item);
        if ($response = $grid->getResponse()) {
            return $response;
        }
    }

    /**
     * Synchronize language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/synchronize/{id:[0-9]+}", name="admin-languages-synchronize")
     */
    public function synchronizeAction($id)
    {
        $item = Language::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->trans->_('Language not found!'));
            return $this->response->redirect(['for' => "admin-languages"]);
        }

        $defaultLanguage = Language::findFirstByLanguage(Config::CONFIG_DEFAULT_LANGUAGE);

        $table = LanguageTranslation::getTableName();
        $defaultLanguageId = $defaultLanguage->getId();

        $result = $this->db->query(
            "
            INSERT INTO `{$table}` (language_id, original, translated, scope)
            SELECT {$id}, original, translated, scope FROM `{$table}`
            WHERE language_id = {$defaultLanguageId} AND original NOT IN
              (SELECT original FROM `{$table}` WHERE language_id = {$id});
            "
        );

        $this->flashSession->success(
            $this->trans->_('Synchronization finished! Added translations: %count%', ['count' => $result->numRows()])
        );
        return $this->response->redirect(['for' => "admin-languages-manage", 'id' => $id]);
    }

    /**
     * Create translation action.
     *
     * @return void
     *
     * @Route("/create-item", methods={"GET", "POST"}, name="admin-languages-create-item")
     */
    public function createItemAction()
    {
        $form = new CreateItem();
        $this->view->form = $form;

        $data = [
            'language_id' => $this->request->get('language_id')
        ];

        $form->setValues($data);
        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->view->created = true;
    }

    /**
     * Edit translation.
     *
     * @param int $id Translation identity.
     *
     * @return void
     *
     * @Route("/edit-item/{id:[0-9]+}", methods={"GET", "POST"}, name="admin-languages-edit-item")
     */
    public function editItemAction($id)
    {
        $item = LanguageTranslation::findFirst($id);
        $form = new EditItem($item);
        $this->view->form = $form;

        $data = [
            'language_id' => $this->request->get('language_id'),
        ];

        $form->setValues($data);
        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->view->edited = true;
    }

    /**
     * Delete translation.
     *
     * @param int $id Translation identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/delete-item/{id:[0-9]+}", name="admin-languages-delete-item")
     */
    public function deleteItemAction($id)
    {
        $item = LanguageTranslation::findFirst($id);
        if ($item) {
            $item->delete();
        }

        $languageId = $this->request->get('lang');
        $search = $this->request->get('search');

        if ($languageId) {
            if (!empty($search)) {
                return $this->response->redirect("admin/languages/manage/{$languageId}?search=" . $search);
            }

            return $this->response->redirect(['for' => "admin-languages-manage", 'id' => $languageId]);
        }

        return $this->response->redirect(['for' => "admin-languages"]);
    }

    /**
     * Compile language into native php array.
     *
     * @return ResponseInterface
     *
     * @Get("/compile", name="admin-languages-compile")
     */
    public function compileAction()
    {
        // Prepare languages.
        // Dump all data from database to files with native php array.
        try {

            $languages = Language::find();
            foreach ($languages as $language) {
                $language->generatePHP();
            }

            $this->flashSession->success($this->trans->_('Languages compilation finished!'));
        } catch (Exception $e) {
            $this->flashSession->error($this->trans->_('Compilation failed, error: <br/>' . $e->getMessage()));
        }

        return $this->response->redirect(['for' => 'admin-languages']);
    }

    /**
     * Check and set icon for language.
     *
     * @param Language $language Language object.
     * @param FileForm $form     Form object.
     *
     * @return void
     */
    protected function _setLanguageIcon($language, $form)
    {
        // Upload language icon.
        if (!$form->hasFiles()) {
            return;
        }

        if (!is_dir(PUBLIC_PATH . '/' . Language::LANGUAGE_ICON_LOCATION)) {
            mkdir(PUBLIC_PATH . '/' . Language::LANGUAGE_ICON_LOCATION, 766, true);
        }

        $files = $form->getFiles();
        $iconPath = Language::LANGUAGE_ICON_LOCATION .
            $language->language .
            '.' . pathinfo($files[0]->getName(), PATHINFO_EXTENSION);
        $fullIconPath = PUBLIC_PATH . '/' . $iconPath;

        if (file_exists($fullIconPath)) {
            @unlink($fullIconPath);
        }

        $files[0]->moveTo($fullIconPath);

        $language->icon = $iconPath;
        $language->save();
    }
}

