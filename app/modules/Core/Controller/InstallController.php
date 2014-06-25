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

use Core\Form\Install\Database as DatabaseForm;
use Core\Form\Install\Finish as FinishForm;
use Core\Model\Package;
use Core\Model\Settings;
use Engine\Asset\Manager as AssetManager;
use Engine\Config;
use Engine\Db\Model\Annotations\Initializer as ModelAnnotationsInitializer;
use Engine\Db\Schema;
use Engine\Package\Manager as PackageManager;
use Phalcon\Assets\Collection as AssetsCollection;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\View;
use User\Model\Role;
use User\Model\User;

/**
 * Installation.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/install", name="installation")
 */
class InstallController extends AbstractController
{
    /**
     * System requirements.
     *
     * @var array
     */
    protected $_requirements = [
        'php' => [
            'version' => '5.4.0',
            'title' => 'PHP v.5.4+'
        ],
        'phalcon' => [
            'version' => PHALCON_VERSION_REQUIRED,
            'title' => "Phalcon v."
        ],
        'zlib' => false,
        'mbstring' => false,
        'mcrypt' => false,
        'iconv' => false,
        'gd' => false,
        'fileinfo' => false,
        'zip' => false,
    ];

    /**
     * Installation actions.
     *
     * @var array
     */
    protected $_actions = [
        'indexAction',
        'requirementsAction',
        'databaseAction',
        'finishAction'
    ];

    /**
     * Initialize installation controller.
     *
     * @return ResponseInterface|void
     */
    public function initialize()
    {
        if (!$this->di->has('installationRequired')) {
            return $this->response->redirect();
        }

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->disableHeader();
        $this->disableFooter();

        $collection = new AssetsCollection();
        $collection->addCss('assets/css/core/install.css');
        $this->assets->set(AssetManager::DEFAULT_COLLECTION_CSS, $collection);
    }

    /**
     * Main installation page.
     *
     * @return void
     *
     * @Route("/", methods={"GET", "POST"}, name="install-index")
     */
    public function indexAction()
    {
        if ($_SERVER['REQUEST_URI'] != $this->config->application->baseUrl && $_SERVER['REQUEST_URI'] != '/install') {
            echo "
            System must be installed.<br/>
            If requested url ('{$_SERVER['REQUEST_URI']}') is root of your application please set it in config.<br/>
            Current configuration base path is '{$this->config->application->baseUrl}'.<br/>
            Please, edit /app/config/" . APPLICATION_STAGE . "/application.php config.

            <br/><br/><br/><br/>
            If you want just to install the system and base url is correct, visit <a href='/'>home</a> page.";
            exit(1);
        }

        // Make sure that all assets installed.
        $this->assets->installAssets();
        $licenseFile = PUBLIC_PATH . '/../LICENSE.txt';


        if (!file_exists($licenseFile)) {
            $this->view->license = 'Missing LICENSE.txt file.
            You can visit
            <a target="_blank" href="https://github.com/lantian/PhalconEye/blob/master/LICENSE.txt">
            GitHub page<a/>
            to read it.';
        } else {

            $this->view->license = str_replace('<', '&#60;', file_get_contents($licenseFile));
        }

        $this->_setPassed(__FUNCTION__, true);
    }

    /**
     * Requirements page.
     *
     * @return void
     *
     * @Route("/requirements", methods={"GET", "POST"}, name="install-requirements")
     */
    public function requirementsAction()
    {
        if (!$this->_isPassed('indexAction')) {
            return $this->_selectAction();
        }

        // Run requirements check.
        $allPassed = true;

        // Modules requirements.
        $requirements = [];
        foreach ($this->_requirements as $req => $version) {
            $title = $req;
            if (is_array($version)) {
                $title = $version['title'];
                $version = $version['version'];
            }

            if ($req == 'phalcon') {
                $title .= $version;
            }

            if ($req == 'php') {
                $passed = version_compare(phpversion(), $version, '>=');
            } else {
                $passed = extension_loaded($req);
                $comparison = '>=';
                if ($passed && $version !== false) {
                    $passed = version_compare(phpversion($req), $version, $comparison);
                }
            }
            $requirements[] = [
                'name' => $title,
                'passed' => $passed
            ];
            $allPassed = $allPassed && $passed;
        }

        // Path is writable?
        $pathInfo = [];
        foreach ($GLOBALS['PATH_REQUIREMENTS'] as $path) {
            if ($path === null) {
                continue;
            }
            $is_writable = is_writable($path);
            $pathInfo[] = [
                'name' => $path,
                'is_writable' => $is_writable
            ];
            $allPassed = $allPassed && $is_writable;
        }

        $this->view->reqs = $requirements;
        $this->view->pathInfo = $pathInfo;
        $this->view->passed = $allPassed;
        $this->_setPassed(__FUNCTION__, $allPassed);
    }

    /**
     * Database installation step.
     *
     * @return mixed
     *
     * @Route("/database", methods={"GET", "POST"}, name="install-database")
     */
    public function databaseAction()
    {
        if (!$this->_isPassed('requirementsAction') || $this->_isPassed('databaseAction')) {
            return $this->_selectAction();
        }

        $form = new DatabaseForm();
        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {
            $data = $form->getValues();

            try {
                $connectionSettings = [
                    "adapter" => $data['adapter'],
                    "host" => $data['host'],
                    "port" => $data['port'],
                    "username" => $data['username'],
                    "password" => $data['password'],
                    "dbname" => $data['dbname'],
                ];

                $this->_setupDatabase($connectionSettings);

                // Install schema.
                $schema = new Schema($this->di);
                $schema->updateDatabase();

                // Run modules installation scripts.
                $packageManager = new PackageManager([], $this->di);
                foreach ($this->di->get('registry')->modules as $moduleName) {
                    $packageManager->runInstallScript(
                        new Config(
                            [
                                'name' => $moduleName,
                                'type' => PackageManager::PACKAGE_TYPE_MODULE,
                                'currentVersion' => '0',
                                'isUpdate' => false
                            ]
                        )
                    );
                }

                $this->config->save('database');
                $this->_setPassed(__FUNCTION__, true);
            } catch (\Exception $ex) {
                $form->addError($ex->getMessage());
            }

            if ($this->_isPassed(__FUNCTION__)) {
                return $this->_selectAction();
            }
        }

        $this->view->form = $form;
    }

    /**
     * Installation finish.
     *
     * @return mixed
     *
     * @Route("/finish", methods={"GET", "POST"}, name="install-finish")
     */
    public function finishAction()
    {
        if (!$this->_isPassed('databaseAction')) {
            return $this->_selectAction();
        }

        $form = new FinishForm();
        if ($this->request->isPost() && $form->isValid()) {

            $password = $this->request->getPost('password', 'string');
            $repeatPassword = $this->request->getPost('repeatPassword', 'string');
            if ($password != $repeatPassword) {
                $form->addError("Passwords doesn't match!");
                $this->view->form = $form;

                return;
            }

            // Setup database.
            $this->_setupDatabase();

            $user = new User();
            $data = $form->getValues();
            $user->role_id = Role::getRoleByType('admin')->id;
            if (!$user->save($data)) {
                foreach ($user->getMessages() as $message) {
                    $form->addError($message);
                }
                $this->view->form = $form;

                return;
            }

            $this->_setPassed(__FUNCTION__, true);

            return $this->response->redirect(['for' => 'install-save']);
        }
        $this->view->form = $form;
    }

    /**
     * Save finish form action.
     *
     * @return ResponseInterface
     *
     * @Route("/save", methods={"GET"}, name="install-save")
     */
    public function saveAction()
    {
        if (!$this->_isPassed('finishAction')) {
            return $this->_selectAction();
        }

        foreach ($this->_actions as $action) {
            $this->_setPassed($action, false);
        }
        $this->_setupDatabase();
        $this->config->offsetSet('installed', true);

        $packageManager = new PackageManager(Package::find());
        $packageManager->generateMetadata();

        $assetsManager = new AssetManager($this->getDI(), false);
        $assetsManager->clear(true, PUBLIC_PATH . '/themes/' . Settings::getSetting('system_theme'));

        return $this->response->redirect();
    }

    /**
     * Set action as (not)passed.
     *
     * @param string $action Action name.
     * @param bool   $passed Is passed variable.
     */
    protected function _setPassed($action, $passed)
    {
        $this->session->set('installation_action_' . $action, $passed);
    }

    /**
     * Check if action was passed successful.
     *
     * @param string $action Action name.
     *
     * @return bool
     */
    protected function _isPassed($action)
    {
        return $this->session->get('installation_action_' . $action);
    }

    /**
     * Choose current action.
     *
     * @return ResponseInterface
     */
    protected function _selectAction()
    {
        foreach ($this->_actions as $action) {
            if (!$this->_isPassed($action)) {
                return $this->response->redirect(
                    ["for" => 'install-' . str_replace('Action', '', $action)]
                );
            }
        }
    }

    /**
     * Setup database connection.
     *
     * @param array|null $connectionSettings Connection data.
     *
     * @return void
     */
    protected function _setupDatabase($connectionSettings = null)
    {
        if ($connectionSettings != null) {
            $this->config->database = new Config($connectionSettings);
        }

        $config = $this->config;
        $eventsManager = new EventsManager();

        $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        $connection = new $adapter(
            [
                "host" => $config->database->host,
                "port" => $config->database->port,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
            ]
        );


        $this->di->set('db', $connection);
        $this->di->set(
            'modelsManager',
            function () use ($config, $eventsManager) {
                $modelsManager = new ModelManager();
                $modelsManager->setEventsManager($eventsManager);

                //Attach a listener to models-manager
                $eventsManager->attach('modelsManager', new ModelAnnotationsInitializer());

                return $modelsManager;
            },
            true
        );
    }
}

