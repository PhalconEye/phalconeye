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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Controller\Backoffice;

use Core\Form\Backoffice\Language\LanguageCreateForm;
use Core\Grid\Backoffice\LanguageGrid;
use Core\Grid\Backoffice\LanguageTranslationGrid;
use Core\Form\Backoffice\Language\LanguageItemCreateForm;
use Core\Form\Backoffice\Language\LanguageEditForm;
use Core\Form\Backoffice\Language\LanguageItemEditForm;
use Core\Form\Backoffice\Language\LanguageExportForm;
use Core\Form\Backoffice\Language\LanguageUploadForm;
use Core\Form\Backoffice\Language\LanguageWizardForm;
use Core\Model\LanguageModel;
use Core\Model\LanguageTranslationModel;
use Core\Navigation\Backoffice\LanguagesNavigation;
use Engine\Config;
use Engine\Exception;
use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Validation\Message;

/**
 * Admin languages controller.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice/languages", name="backoffice-languages")
 */
class LanguagesController extends AbstractBackofficeController
{
    /**
     * Init controller.
     *
     * @return void
     */
    public function init()
    {
        $this->view->navigation = new LanguagesNavigation;
    }

    /**
     * Index action.
     *
     * @return void
     *
     * @Get("/", name="backoffice-languages")
     */
    public function indexAction()
    {
        $this->view->form = new LanguageUploadForm();
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
     * @Route("/create", methods={"GET", "POST"}, name="backoffice-languages-create")
     */
    public function createAction()
    {
        $this->view->form = $form = new LanguageCreateForm();

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        /** @var LanguageModel $language */
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
        return $this->response->redirect(['for' => "backoffice-languages"]);
    }

    /**
     * Edit language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Route("/edit/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-languages-edit")
     */
    public function editAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if (!$item) {
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        if ($item->language == Config::CONFIG_DEFAULT_LANGUAGE && $item->locale = Config::CONFIG_DEFAULT_LOCALE) {
            $this->flashSession->notice('Not allowed to edit default language!');
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        $form = new LanguageEditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        /** @var LanguageModel $language */
        $language = $form->getEntity();

        // Check uploaded files.
        $this->_setLanguageIcon($language, $form);

        $this->flashSession->success('Object saved!');

        return $this->response->redirect(['for' => "backoffice-languages"]);
    }

    /**
     * Delete language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/delete/{id:[0-9]+}", name="backoffice-languages-delete")
     */
    public function deleteAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if ($item) {
            if ($item->language == Config::CONFIG_DEFAULT_LANGUAGE && $item->locale = Config::CONFIG_DEFAULT_LOCALE) {
                $this->flashSession->notice('Not allowed to delete default language!');
                return $this->response->redirect(['for' => "backoffice-languages"]);
            }

            if ($item->delete()) {
                $this->flashSession->notice('Object deleted!');
            } else {
                $this->flashSession->error($item->getMessages());
            }
        }

        return $this->response->redirect(['for' => "backoffice-languages"]);
    }

    /**
     * Manage language action.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/manage/{id:[0-9]+}", name="backoffice-languages-manage")
     */
    public function manageAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->i18n->_('Language not found!'));
            return $this->response->redirect(['for' => "backoffice-languages"]);
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
     * @Get("/synchronize/{id:[0-9]+}", name="backoffice-languages-synchronize")
     */
    public function synchronizeAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->i18n->_('Language not found!'));
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        $defaultLanguage = LanguageModel::findFirstByLanguage(Config::CONFIG_DEFAULT_LANGUAGE);
        $result = LanguageTranslationModel::copyTranslations($id, $defaultLanguage->getId());

        $this->flashSession->success(
            $this->i18n->_('Synchronization finished! Added translations: %count%', ['count' => $result->numRows()])
        );
        return $this->response->redirect(['for' => "backoffice-languages-manage", 'id' => $id]);
    }

    /**
     * Import language translations.
     *
     * @return void|ResponseInterface
     *
     * @Route("/import", methods={"POST"}, name="backoffice-languages-import")
     */
    public function importAction()
    {
        $form = new LanguageUploadForm();
        $file = $this->request->getUploadedFiles();
        if (!$this->request->isPost() || !$form->isValid() || empty($file)) {
            $messages = [];

            if (empty($file)) {
                $messages[] = $this->i18n->_("Missing file.");
            }

            foreach ($form->getErrors() as $error) {
                if ($error instanceof Message) {
                    $error = $error->getMessage();
                }
                $messages[] = $this->i18n->_($error);
            }
            $this->flashSession->error($this->i18n->_('There are errors:') . '<br>' . implode('<br>', $messages));
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        try {
            /**
             * Parse file.
             */
            $data = json_decode(file_get_contents($file[0]->getTempName()), true);
            list ($language, $totals) = LanguageModel::parseImportData($this->getDI(), $data);

            $message = sprintf(
                $this->i18n->_('<br/>Language "%s" (%s). Imported totals (scope: count): <br/>'),
                $language->language,
                $language->locale
            );
            foreach ($totals as $scope => $count) {
                $message .= '&nbsp;-&nbsp;' . $scope . ': ' . $count . '<br/>';
            }

            $this->flashSession->success($this->i18n->_('Language translations has been imported!') . $message);
        } catch (Exception $e) {
            $this->flashSession->error($this->i18n->_($e->getMessage()));
        }

        return $this->response->redirect(['for' => "backoffice-languages"]);
    }

    /**
     * Export language translations.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Route("/export/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-languages-export")
     */
    public function exportAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->i18n->_('Language not found!'));
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        $form = new LanguageExportForm($item);
        $this->view->form = $form;
        $this->hideFooter();

        if (!$this->request->isPost()) {
            return;
        }

        $scope = $this->request->get('scope', null, []);
        header("Content-disposition: attachment; filename=language-{$item->language}-{$item->locale}.json");
        header('Content-type: application/json');

        $response = new Response();
        $response->setContent($item->toJson($scope));
        return $response;
    }

    /**
     * Wizard for language.
     *
     * @param int $id Language identity.
     *
     * @return void|ResponseInterface
     *
     * @Route("/wizard/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-languages-wizard")
     */
    public function wizardAction($id)
    {
        $item = LanguageModel::findFirst($id);
        if (!$item) {
            $this->flashSession->error($this->i18n->_('Language not found!'));
            return $this->response->redirect(['for' => "backoffice-languages"]);
        }

        if ($this->request->isPost()) {
            $translationId = $this->request->getPost('translation_id');
            $translation = LanguageTranslationModel::findFirstById($translationId);
            if ($translation) {
                $translation->translated = $this->request->getPost('translated');
                $translation->checked = true;
                $translation->save();
            }
        }

        $condition = 'original = translated AND checked = 0 AND language_id = ' . $id;
        $this->hideFooter();
        $this->view->form = $form = new LanguageWizardForm($item);
        $this->view->total = LanguageTranslationModel::find([$condition])->count();
        $this->view->translation = $translation =
            LanguageTranslationModel::findFirst([$condition]);
        $this->view->item = $item;

        if ($translation) {
            $form->setValues($translation->toArray());
            $form->setValue('translation_id', $translation->getId());
        }
    }

    /**
     * Create translation action.
     *
     * @return void
     *
     * @Route("/create-item", methods={"GET", "POST"}, name="backoffice-languages-create-item")
     */
    public function createItemAction()
    {
        $form = new LanguageItemCreateForm();
        $this->view->form = $form;

        $data = [
            'language_id' => $this->request->get('language_id')
        ];

        $form->setValues($data);
        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->resolveModal();
    }

    /**
     * Edit translation.
     *
     * @param int $id Translation identity.
     *
     * @return void
     *
     * @Route("/edit-item/{id:[0-9]+}", methods={"GET", "POST"}, name="backoffice-languages-edit-item")
     */
    public function editItemAction($id)
    {
        $item = LanguageTranslationModel::findFirst($id);
        $form = new LanguageItemEditForm($item);
        $this->view->form = $form;

        if (!$this->request->isPost() || !$form->isValid()) {
            return;
        }

        $this->resolveModal();
    }

    /**
     * Delete translation.
     *
     * @param int $lang Language identity.
     * @param int $id   Translation identity.
     *
     * @return void|ResponseInterface
     *
     * @Get("/delete-item/{lang:[0-9]+}/{id:[0-9]+}", name="backoffice-languages-delete-item")
     */
    public function deleteItemAction($lang, $id)
    {
        $item = LanguageTranslationModel::findFirst($id);
        if ($item) {
            $item->delete();
        }

        if ($lang) {
            return $this->response->redirect(['for' => "backoffice-languages-manage", 'id' => $lang]);
        }

        return $this->response->redirect(['for' => "backoffice-languages"]);
    }

    /**
     * Compile language into native php array.
     *
     * @return ResponseInterface
     *
     * @Get("/compile", name="backoffice-languages-compile")
     */
    public function compileAction()
    {
        // Prepare languages.
        // Dump all data from database to files with native php array.
        try {
            $languages = LanguageModel::find();
            $directory = $this->config->core->languages->cacheDir;

            foreach ($languages as $language) {
                $file = $directory . '../languages/' . $language->language . '.php';
                file_put_contents($file, $language->toPhp());
            }

            $this->flashSession->success($this->i18n->_('Languages compilation finished!'));
        } catch (Exception $e) {
            $this->flashSession->error($this->i18n->_('Compilation failed, error: <br />' . $e->getMessage()));
        }

        return $this->response->redirect(['for' => 'backoffice-languages']);
    }

    /**
     * Check and set icon for language.
     *
     * @param LanguageModel $language Language object.
     * @param FileForm      $form     Form object.
     *
     * @return void
     */
    protected function _setLanguageIcon($language, $form)
    {
        // Upload language icon.
        if (!$form->hasFiles()) {
            return;
        }

        if (!is_dir(PUBLIC_PATH . '/' . LanguageModel::LANGUAGE_ICON_LOCATION)) {
            mkdir(PUBLIC_PATH . '/' . LanguageModel::LANGUAGE_ICON_LOCATION, 766, true);
        }

        $files = $form->getFiles();
        $iconPath = LanguageModel::LANGUAGE_ICON_LOCATION .
            $language->language .
            ' . ' . pathinfo($files[0]->getName(), PATHINFO_EXTENSION);
        $fullIconPath = PUBLIC_PATH . '/' . $iconPath;

        if (file_exists($fullIconPath)) {
            @unlink($fullIconPath);
        }

        $files[0]->moveTo($fullIconPath);

        $language->icon = $iconPath;
        $language->save();
    }
}

