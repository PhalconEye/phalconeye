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


namespace Core\Controller;

use Core\Controller\Base as PeController;
use Engine\Asset\Manager as AssetManager;
use Engine\Db\Model\Annotations\Initializer as ModelAnnotationsInitializer;
use Engine\Db\Schema;
use Engine\Form\Validator\Email;
use Engine\Form\Validator\StringLength;
use Engine\Package\Manager as PackageManager;
use Phalcon\Config;
use Phalcon\Mvc\View as PhView;

class InstallController extends PeController
{

    protected $_requirements = array(
//        name => version
        'php' => '5.4.0',
        'phalcon' => PHALCON_VERSION_REQUIRED,
        'zlib' => false,
        'mbstring' => false,
        'mcrypt' => false,
        'iconv' => false,
    );

    protected $_actions = array(
        'indexAction',
        'databaseAction',
        'finishAction'
    );

    public function initialize()
    {
        if (!$this->di->has('installationRequired')) {
            return $this->response->redirect();
        }

        $this->view->setRenderLevel(PhView::LEVEL_ACTION_VIEW);
        $this->disableHeader();
        $this->disableFooter();

        $collection = new \Phalcon\Assets\Collection();
        $collection->addCss('assets/css/core/install.css');
        $this->assets->set('css', $collection);
    }

    /**
     * @Route("/install", methods={"GET", "POST"}, name="install-index")
     */
    public function indexAction()
    {
        // run requirements check
        $allPassed = true;

        // Modules requirements
        $requirements = array();
        foreach ($this->_requirements as $req => $version) {
            $installedVersion = false;
            if ($req == 'php') {
                $installedVersion = phpversion();
                $passed = version_compare($installedVersion, $version, '>=');
            } else {
                $passed = extension_loaded($req);
                $comparison = '>=';
                if ($passed && $version !== false) {
                    $installedVersion = phpversion($req);
                    $passed = version_compare($installedVersion, $version, $comparison);
                }
            }
            $requirements[] = array(
                'name' => $req,
                'version' => $version,
                'installed_version' => $installedVersion,
                'passed' => $passed
            );
            $allPassed = $allPassed && $passed;
        }

        // Path is writable?
        $pathInfo = array();
        foreach ($GLOBALS['PATH_REQUIREMENTS'] as $path) {
            $is_writable = is_writable($path);
            $pathInfo[] = array(
                'name' => $path,
                'is_writable' => $is_writable
            );
            $allPassed = $allPassed && $is_writable;
        }

        $this->view->reqs = $requirements;
        $this->view->pathInfo = $pathInfo;
        $this->view->passed = $allPassed;
        $this->_setPassed(__FUNCTION__, $allPassed);
    }

    /**
     * @Route("/install/database", methods={"GET", "POST"}, name="install-database")
     */
    public function databaseAction()
    {
        if (!$this->_isPassed('indexAction') || $this->_isPassed('databaseAction')) {
            return $this->_selectAction();
        }

        $form = $this->_getDatabaseForm();

        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {
            $data = $form->getValues();

            try {
                $connectionSettings = array(
                    "adapter" => $data['adapter'],
                    "host" => $data['host'],
                    "username" => $data['username'],
                    "password" => $data['password'],
                    "dbname" => $data['dbname'],
                );

                $this->_setupDatabase($connectionSettings);

                // Install schema.
                $schema = new Schema($this->di);
                $schema->updateDatabase();

                // Run modules installation scripts.
                $packageManager = new PackageManager(array(), $this->di);
                foreach ($this->di->get('modules') as $moduleName => $enabled) {
                    if (!$enabled) {
                        continue;
                    }

                    $packageManager->runInstallScript(
                        new Config(
                            array(
                                'name' => $moduleName,
                                'type' => PackageManager::PACKAGE_TYPE_MODULE,
                                'currentVersion' => '0',
                                'isUpdate' => false
                            )
                        )
                    );
                }

                $this->app->saveConfig($this->config);
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
     * @Route("/install/finish", methods={"GET", "POST"}, name="install-finish")
     */
    public function finishAction()
    {
        if (!$this->_isPassed('databaseAction')) {
            return $this->_selectAction();
        }

        $form = $this->_getFinishForm();
        if ($this->request->isPost() && $form->isValid($this->request->getPost())) {

            $password = $this->request->getPost('password', 'string');
            $repeatPassword = $this->request->getPost('password', 'string');
            if ($password != $repeatPassword) {
                $form->addError("Passwords doesn't match!");
                $this->view->form = $form;
                return;
            }

            // Setup database.
            $this->_setupDatabase();

            $user = new \User\Model\User();
            $data = $form->getValues();
            $user->role_id = \User\Model\Role::getRoleByType('admin')->id;
            if (!$user->save($data)) {
                foreach ($user->getMessages() as $message) {
                    $form->addError($message);
                }
                $this->view->form = $form;
                return;
            }

            $this->_setPassed(__FUNCTION__, true);
            return $this->response->redirect(array('for' => 'install-save'));
        }
        $this->view->form = $form;
    }

    /**
     * @Route("/install/save", methods={"GET"}, name="install-save")
     */
    public function saveAction()
    {
        if (!$this->_isPassed('finishAction')) {
            return $this->_selectAction();
        }

        $this->_resetStates();
        $this->config->installed = true;
        $this->config->installedVersion = PE_VERSION;
        $this->app->saveConfig();

        // Setup database to perform theme installation.
        $this->_setupDatabase();
        $assetsManager = new AssetManager($this->getDI(), false);
        $assetsManager->installAssets();
        return $this->response->redirect();
    }

    /**
     * Get database form.
     *
     * @return \Engine\Form
     */
    private function _getDatabaseForm()
    {
        $form = new \Engine\Form();
        $form->setOption('title', 'Database settings');

        $form->addElement('select', 'adapter', array(
            'label' => 'Database adapter',
            'options' => array(
                'Mysql' => 'MySQL',
                'Oracle' => 'Oracle',
                'Postgresql' => 'PostgreSQL',
                'Sqlite' => 'SQLite'
            ),
            'value' => 'Mysql'
        ));

        $form->addElement('text', 'host', array(
            'label' => 'Database host',
            'value' => 'localhost'
        ));

        $form->addElement('text', 'username', array(
            'label' => 'Username',
            'value' => 'root'
        ));

        $form->addElement('password', 'password', array(
            'label' => 'Password',
        ));

        $form->addElement('text', 'dbname', array(
            'label' => 'Database name',
            'value' => 'phalconeye'
        ));

        $form->addButton('Continue', true);

        return $form;
    }

    /**
     * Get finish form.
     *
     * @return \Engine\Form
     */
    private function _getFinishForm()
    {
        $form = new \Engine\Form();

        $form->addElement('text', 'username', array(
            'label' => 'Username',
            'autocomplete' => 'off',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 2,
                ))
            )
        ));

        $form->addElement('text', 'email', array(
            'label' => 'Email',
            'autocomplete' => 'off',
            'description' => 'You will use your email address to login.',
            'required' => true,
            'validators' => array(
                new Email()
            )
        ));

        $form->addElement('password', 'password', array(
            'label' => 'Password',
            'autocomplete' => 'off',
            'description' => 'Passwords must be at least 6 characters in length.',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $form->addElement('password', 'repeatPassword', array(
            'label' => 'Password Repeat',
            'autocomplete' => 'off',
            'description' => 'Enter your password again for confirmation.',
            'required' => true,
            'validators' => array(
                new StringLength(array(
                    'min' => 6,
                ))
            )
        ));

        $form->addButton('Complete', true);
        return $form;
    }

    /**
     * Set action as (not)passed.
     *
     * @param string $action Action name.
     * @param bool   $passed Is passed variable.
     */
    private function _setPassed($action, $passed)
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
    private function _isPassed($action)
    {
        return $this->session->get('installation_action_' . $action);
    }

    /**
     * Choose current action.
     *
     * @return mixed
     */
    private function _selectAction()
    {
        foreach ($this->_actions as $action) {
            if (!$this->_isPassed($action)) {
                return $this->response->redirect(array(
                    "for" => 'install-' . str_replace('Action', '', $action)
                ));
            }
        }
    }

    /**
     * Reset action states.
     */
    private function _resetStates()
    {
        foreach ($this->_actions as $action) {
            $this->_setPassed($action, false);
        }
    }

    /**
     * Setup database connection.
     */
    private function _setupDatabase($connectionSettings = null)
    {
        if ($connectionSettings != null) {
            $this->config->database = new \Phalcon\Config($connectionSettings);
        }

        $config = $this->config;
        $eventsManager = new \Phalcon\Events\Manager();

        $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
        $connection = new $adapter(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
        ));

        $this->di->set('db', $connection);

        $this->di->set('modelsManager', function () use ($config, $eventsManager) {
            $modelsManager = new \Phalcon\Mvc\Model\Manager();
            $modelsManager->setEventsManager($eventsManager);

            //Attach a listener to models-manager
            $eventsManager->attach('modelsManager', new ModelAnnotationsInitializer());

            return $modelsManager;
        }, true);
    }
}

