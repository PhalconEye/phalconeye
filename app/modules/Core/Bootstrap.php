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

namespace Core;

use Phalcon\DiInterface,
    Phalcon\Translate\Adapter\NativeArray as TranslateArray;

class Bootstrap extends \Engine\Bootstrap
{
    protected $_moduleName = "Core";

    public static function dependencyInjection(DiInterface $di, \Phalcon\Config $config)
    {
        self::_initWidgets($di);
        self::_initLocale($di);

        if ($config->application->debug) {
            $di->get('assets')
                ->collection('css')
                ->addCss('external/phalconeye/css/profiler.css');
            ;

            $di->get('assets')
                ->collection('js')
                ->addCss('external/phalconeye/js/profiler.js');
            ;
        }
    }

    /**
     * Prepare widgets metadata for Engine
     */
    private static function _initWidgets(\Phalcon\DI $di)
    {
        $cache = $di->get('cacheData');
        $cacheKey = "widgets_metadata.cache";
        $widgets = $cache->get($cacheKey);

        if ($widgets === null) {
            $widgetObjects = \Core\Model\Widget::find();
            $widgets = array();
            foreach ($widgetObjects as $object) {
                $widgets[$object->id] = $object;
            }

            $cache->save($cacheKey, $widgets, 2592000); // 30 days
        }
        \Engine\Widget\Storage::setWidgets($widgets);
    }

    /**
     * Init locale
     *
     * @param $di
     */
    private static function _initLocale(\Phalcon\DI $di)
    {
        $locale = $di->get('session')->get('locale', \Core\Model\Settings::getSetting('system_default_language'));
        $translate = null;

        if (!$di->get('config')->application->debug) {
            $messages = array();
            if (file_exists(ROOT_PATH . "/app/var/languages/" . $locale . ".php")) {
                require ROOT_PATH . "/app/var/languages/" . $locale . ".php";
            } else {
                if (file_exists(ROOT_PATH . "/app/var/languages/en.php")) {
                    // fallback to default
                    require ROOT_PATH . "/app/var/languages/en.php";
                }
            }

            $translate = new TranslateArray(array(
                "content" => $messages
            ));
        } else {
            $translate = new \Engine\Translation\Db(array(
                'db' => $di->get('db'),
                'locale' => $locale,
                'model' => 'Core\Model\Language',
                'translationModel' => 'Core\Model\LanguageTranslation'
            ));
        }

        $di->set('trans', $translate);
    }

    public static function handleProfiler(\Phalcon\DI $di, \Phalcon\Config $config)
    {
        if (!$config->application->debug || !$di->has('profiler')){
            return;
        }

        // check admin area
        if (substr($di->get('dispatcher')->getControllerName(),0, 5) == 'admin'){
            return;
        }

        $viewer = \User\Model\User::getViewer();
        if (!$viewer->id || !$viewer->isAdmin()){
            return;
        }


        $profiler = $di->get('profiler');
        $dbProfiler = $profiler->getDbProfiler();
        $router = $di->get('router');
        $dbProfiles = $dbProfiler->getProfiles();

        $html = '<div class="profiler"><div window="config" class="item"><img alt="Phalcon Eye Profiler" src="/favicon.ico"/></div>%s%s%s%s%s%s</div>';
        $htmlWindow = '<div id="profiler_window_%s" class="profiler_window"><div class="profiler_window_title">%s<a href="javascript:;" class="profiler_window_close"></a></div><div class="profiler_window_body">%s</div></div>';

        // Config
        $configData = $config->toArray();
        $description = '';
        foreach($configData as $key => $data){
            if (empty($data)){
                continue;
            }

            $description .= '<h2 class="label">'.ucfirst($key).'</h2>';
            foreach($data as $key2 => $data2){
                if (is_array($data2)){
                    foreach($data2 as $key3 => $data3){
                        if (!is_array($data2)){
                            $description .= '<span class="label">'.ucfirst($key2).': </span><span class="code">'.$data2.'</span><br/>';
                        }
                    }
                }
                else{
                    $description .= '<span class="label">'.ucfirst($key2).': </span><span class="code">'.$data2.'</span><br/>';
                }
            }

            $description .= '<br/>';
        }

        echo sprintf($htmlWindow, 'config', 'Phalcon Eye Config', $description);

        // Router
        $handler = '<div window="router" class="item">' . ucfirst($router->getControllerName()) . 'Controller::' . ucfirst($router->getActionName()) . 'Action</div>';
        $description = '';
        $description .= '<span class="label">POST data</span><pre class="code">' . print_r($_POST, true) . '</pre>';
        $description .= '<span class="label">GET data</span><pre class="code">' . print_r($_GET, true) . '</pre>';
        $description .= '<span class="label">Module: </span><span class="code">' . ucfirst($router->getModuleName()) . 'Controller</span><br/>';
        $description .= '<span class="label">Controller: </span><span class="code">' . ucfirst($router->getControllerName()) . 'Controller</span><br/>';
        $description .= '<span class="label">Action: </span><span class="code">' . ucfirst($router->getActionName()) . 'Action</span><br/>';
        $description .= '<span class="label">Matched Route: </span><span class="code">' . ucfirst($router->getMatchedRoute()->getName()) . '</span><br/>';
        echo sprintf($htmlWindow, 'router', 'Router', $description);

        // Memory
        $memoryData = memory_get_usage();
        $memoryLimit = ((int)ini_get('memory_limit')) * 1024 * 1024;
        $currentMemoryPercent = round($memoryData / ($memoryLimit / 100));
        $colorClass = ($currentMemoryPercent > 30 ? ($currentMemoryPercent < 75 ? 'item-normal' : 'item-bad') : 'item-good');
        $memory = '<div window="memory" class="item item-right item-memory ' . $colorClass . '">' . round($memoryData / 1024, 2) . ' Kb</div>';

        $description = '';
        foreach (\Engine\Profiler::$objectTypes as $type) {
            $data = $profiler->getData('memory', $type);
            if (empty($data)) {
                continue;
            }

            $description .= '<span class="label">' . ucfirst($type) . 's</span><br/>';
            foreach ($data as $class => $memoryValue) {
                $msTime = round($memoryValue / 1024, 2);
                $description .= '<span class="code">' . str_replace(ROOT_PATH, '', $class) . '</span> <span class="label">' . $msTime . ' kb</span><br/>';
            }

            $description .= '<br/>';
        }
        echo sprintf($htmlWindow, 'memory', 'Memory', $description);

        // Time
        $timeData = round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2);
        $colorClass = ($timeData > 200 ? ($timeData < 500 ? 'item-normal' : 'item-bad') : 'item-good');
        $time = '<div window="time" class="item item-right item-time ' . $colorClass . '">' . $timeData . ' ms</div>';
        $description = '';
        foreach (\Engine\Profiler::$objectTypes as $type) {
            $data = $profiler->getData('time', $type);
            if (empty($data)) {
                continue;
            }

            $description .= '<span class="label">' . ucfirst($type) . 's</span><br/>';
            foreach ($data as $class => $timeValue) {
                $msTime = round($timeValue * 1000, 2);
                $description .= '<span class="code">' . str_replace(ROOT_PATH, '', $class) . '</span> <span class="label">' . $msTime . ' ms</span><br/>';
            }

            $description .= '<br/>';
        }
        echo sprintf($htmlWindow, 'time', 'Time', $description);

        // Files
        $filesData = get_included_files();
        $files = '<div window="files" class="item item-right item-files">' . count($filesData) . '</div>';
        $description = '';
        foreach ($filesData as $file) {
            $filesize = round(filesize($file) / 1024, 2);
            $description .= '<span class="code">' . str_replace(ROOT_PATH, '', $file) . '</span> <span class="label">' . $filesize . ' Kb</span><br/>';
        }
        echo sprintf($htmlWindow, 'files', 'Files', $description);

        // SQL
        $sql = '<div window="sql" class="item item-right item-sql">' . $dbProfiler->getNumberTotalStatements() . '</div>';
        $description = 'No Sql';
        if (!empty($dbProfiles)) {
            $description = '<span class="label">Total count: </span>' . $dbProfiler->getNumberTotalStatements() . '<br/>';
            $description .= '<span class="label">Total time: </span>' . round($dbProfiler->getTotalElapsedSeconds() * 1000, 4) . ' ms<br/><br/>';

            foreach ($dbProfiles as $profile) {
                $description .= '<span class="label">SQL: </span><span class="code">' . $profile->getSQLStatement() . '</span><br/>';
                $description .= '<span class="label">Time: </span>' . round($profile->getTotalElapsedSeconds() * 1000, 4) . ' ms<br/><br/>';
            }
        }
        echo sprintf($htmlWindow, 'sql', 'Sql Statements', $description);

        // Errors
        $errorsData = $profiler->getData('error');
        $errorsCount = count($errorsData);
        $colorClass = ($errorsCount == 0 ? 'item-good' : 'item-bad');
        $errors = '<div window="errors" class="item item-right item-errors ' . $colorClass . '">' . $errorsCount . '</div>';
        $description = 'No Errors';
        foreach ($errorsData as $data) {
            $description .= '<span class="label">' . $data['error'] . '</span><span class="code">' . str_replace('#', '<br/>#', $data['trace']) . '</span><br/><br/>';
        }
        echo sprintf($htmlWindow, 'errors', 'Errors', $description);


        echo sprintf($html, $handler, $errors, $sql, $files, $time, $memory);


    }
}