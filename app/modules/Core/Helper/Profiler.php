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

namespace Core\Helper;

use Engine\Config;
use Engine\Helper;
use Engine\Profiler as EngineProfiler;
use Phalcon\DI;
use Phalcon\Mvc\View;
use Phalcon\Tag;
use User\Model\User;

/**
 * Output profiler info
 *
 * @category  PhalconEye
 * @package   Core\Helper
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Profiler extends Helper
{
    /**
     * View object.
     *
     * @var View
     */
    protected $_view;

    /**
     * Config object.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Render profiler.
     *
     * @return string
     */
    public function render()
    {
        $di = $this->getDI();
        $this->_config = $di->get('config');
        $this->_view = $di->get('view');
        if (!$di->has('profiler')) {
            return '';
        }

        // check admin area
        if (substr($di->get('dispatcher')->getControllerName(), 0, 5) == 'Admin') {
            return '';
        }

        $viewer = User::getViewer();
        if (!$viewer->id || !$viewer->isAdmin()) {
            return '';
        }

        $profiler = $di->get('profiler');
        $router = $di->get('router');
        $dbProfiler = $profiler->getDbProfiler();
        $handlerValues = [];


        //////////////////////////////////////
        /// Router.
        //////////////////////////////////////
        $handlerValues['router'] = ucfirst($router->getControllerName()) .
            'Controller::' .
            ucfirst($router->getActionName()) . 'Action';

        //////////////////////////////////////
        /// Memory.
        //////////////////////////////////////
        $memoryData = memory_get_usage();
        $memoryLimit = ((int)ini_get('memory_limit')) * 1024 * 1024;
        $currentMemoryPercent = round($memoryData / ($memoryLimit / 100));
        $colorClass = (
        $currentMemoryPercent > 30 ? ($currentMemoryPercent < 75 ?
            'item-normal' : 'item-bad') :
            'item-good'
        );
        $handlerValues['memory'] = [
            'class' => $colorClass,
            'value' => round($memoryData / 1024, 2)
        ];


        //////////////////////////////////////
        /// Time.
        //////////////////////////////////////
        $timeData = round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2);
        $colorClass = ($timeData > 200 ? ($timeData < 500 ? 'item-normal' : 'item-bad') : 'item-good');
        $handlerValues['time'] = [
            'class' => $colorClass,
            'value' => $timeData
        ];


        //////////////////////////////////////
        /// Files.
        //////////////////////////////////////
        $filesData = get_included_files();
        $handlerValues['files'] = count($filesData);


        //////////////////////////////////////
        /// SQL.
        //////////////////////////////////////
        $handlerValues['sql'] = $totalSqlStatements = $dbProfiler->getNumberTotalStatements();


        //////////////////////////////////////
        /// Errors.
        //////////////////////////////////////
        $errorsData = $profiler->getData('error');
        $errorsCount = count($errorsData);
        $colorClass = ($errorsCount == 0 ? 'item-good' : 'item-bad');
        $handlerValues['errors'] = [
            'class' => $colorClass,
            'value' => $errorsCount
        ];


        $output = $this->_viewRender(
            'main',
            [
                'handlerValues' => $handlerValues,
                'htmlConfig' => $this->_getHtmlConfig(),
                'htmlRouter' => $this->_getHtmlRouter(),
                'htmlMemory' => $this->_getHtmlMemory(),
                'htmlTime' => $this->_getHtmlTime($timeData),
                'htmlFiles' => $this->_getHtmlFiles($filesData),
                'htmlSql' => $this->_getHtmlSql($dbProfiler, $totalSqlStatements),
                'htmlErrors' => $this->_getHtmlErrors($errorsData, $errorsCount),
            ]
        );

        return trim(preg_replace('/\s\s+/', ' ', $output));
    }

    /**
     * Render template.
     *
     * @param string $template Template name.
     * @param array  $params   Template params.
     *
     * @return string
     */
    private function _viewRender($template, $params)
    {
        ob_start();

        $viewsDir = $this->_view->getViewsDir();
        $this->_view->setViewsDir(ROOT_PATH . '/app/modules/Core/View/');
        $this->_view->partial('partials/profiler/' . $template, $params);
        $this->_view->setViewsDir($viewsDir);

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * Render title template.
     *
     * @param string $title Title.
     *
     * @return string
     */
    private function _viewRenderTitle($title)
    {
        return $this->_viewRender('title', ['title' => $title]);
    }

    /**
     * Render element template.
     *
     * @param string      $title  Title.
     * @param mixed       $value  Template value.
     * @param string|null $tag    Tag name in template.
     * @param bool|null   $noCode No code in template.
     *
     * @return string
     */
    private function _viewRenderElement($title, $value = null, $tag = null, $noCode = null)
    {
        return $this->_viewRender(
            'element',
            ['title' => $title, 'value' => $value, 'tag' => $tag, 'noCode' => $noCode]
        );
    }

    /**
     * Helper for config section rendering.
     *
     * @param array $source Source data.
     *
     * @return string
     */
    private function _renderConfigSection($source)
    {
        $html = '';
        foreach ($source as $key => $data) {
            if (is_array($data)) {
                $html .= '<br/>' . $this->_viewRenderTitle(ucfirst($key));
                $html .= $this->_renderConfigSection($data);
            } else {
                if (is_bool($data)) {
                    $data = $data ? 1 : 0;
                }
                $html .= $this->_viewRenderElement(ucfirst($key), $data);
            }
        }

        return $html;
    }

    /**
     * Get html for config.
     *
     * @return string
     */
    private function _getHtmlConfig()
    {
        $html = '';
        $configData = $this->_config->toArray();
        uasort(
            $configData['application'],
            function ($a, $b) {
                if (is_array($a) && !is_array($b)) {
                    return 1;
                }

                return 0;
            }
        );

        foreach ($configData as $key => $data) {
            if (!is_array($data) || empty($data) || $key == 'database') {
                continue;
            }

            $html .= $this->_viewRenderTitle(ucfirst($key));
            $html .= $this->_renderConfigSection($data);
            $html .= '<br/>';
        }

        return $html;
    }

    /**
     * Render router section.
     *
     * @return string
     */
    private function _getHtmlRouter()
    {
        $router = $this->getDI()->get('router');
        $html = $this->_viewRenderElement('POST data', print_r($_POST, true), 'pre');
        $html .= $this->_viewRenderElement('GET data', print_r($_GET, true), 'pre');
        $html .= $this->_viewRenderElement('Module', ucfirst($router->getModuleName()));
        $html .= $this->_viewRenderElement('Controller', ucfirst($router->getControllerName()));
        $html .= $this->_viewRenderElement('Action', ucfirst($router->getActionName()));
        if ($router->getMatchedRoute()) {
            $html .= $this->_viewRenderElement('Matched Route', ucfirst($router->getMatchedRoute()->getName()));
        }

        return $html;
    }

    /**
     * Get html memory.
     *
     * @return string
     */
    private function _getHtmlMemory()
    {
        $html = '';
        $profiler = $this->getDI()->get('profiler');
        foreach (EngineProfiler::$objectTypes as $type) {
            $data = $profiler->getData('memory', $type);
            if (empty($data)) {
                continue;
            }

            $html .= $this->_viewRenderTitle(ucfirst($type));
            foreach ($data as $class => $memoryValue) {
                $memory = round($memoryValue / 1024, 2);
                $html .= $this->_viewRenderElement(str_replace(ROOT_PATH, '', $class), $memory . ' kb');
            }

            $html .= '<br/>';
        }

        return $html;
    }

    /**
     * Render time section.
     *
     * @param float $timeData Time data.
     *
     * @return string
     */
    private function _getHtmlTime($timeData)
    {
        $html = '';
        $profiler = $this->getDI()->get('profiler');
        foreach (EngineProfiler::$objectTypes as $type) {
            $data = $profiler->getData('time', $type);
            if (empty($data)) {
                continue;
            }

            $html .= $this->_viewRenderTitle(ucfirst($type));
            foreach ($data as $class => $timeValue) {
                $msTime = round($timeValue * 1000, 2);
                $timeData -= $msTime;
                $html .= $this->_viewRenderElement(str_replace(ROOT_PATH, '', $class), $msTime . ' ms');
            }

            $html .= '<br/>';
        }
        $html .= $this->_viewRenderTitle('Other');
        $html .= $this->_viewRenderElement('Time from request received', $timeData . ' ms');
        $html .= '<br/>';

        return $html;
    }

    /**
     * Render files section.
     *
     * @param array $filesData Files data.
     *
     * @return string
     */
    private function _getHtmlFiles($filesData)
    {
        $html = '';
        foreach ($filesData as $file) {
            $filesize = round(filesize($file) / 1024, 2);
            $html .= $this->_viewRenderElement(str_replace(ROOT_PATH, '', $file), $filesize . ' kb');
        }

        return $html;
    }

    /**
     * Get html for sql section.
     *
     * @param \Phalcon\Db\Profiler $dbProfiler         Database profiler.
     * @param int                  $totalSqlStatements Total count.
     *
     * @return string
     */
    private function _getHtmlSql($dbProfiler, $totalSqlStatements)
    {
        $html = 'No Sql';
        $dbProfiles = $dbProfiler->getProfiles();

        if (!empty($dbProfiles)) {
            $longestQuery = '';
            $longestQueryTime = 0;

            $html = $this->_viewRenderElement('Total count', $totalSqlStatements, null, true);
            $html .= $this->_viewRenderElement(
                'Total time',
                round($dbProfiler->getTotalElapsedSeconds() * 1000, 4),
                null,
                true
            );
            $html .= $this->_viewRenderElement(
                'Longest query',
                '<span class="code">%s</span> (%s ms)<br/>',
                null,
                true
            );

            foreach ($dbProfiles as $profile) {
                if ($profile->getTotalElapsedSeconds() > $longestQueryTime) {
                    $longestQueryTime = $profile->getTotalElapsedSeconds();
                    $longestQuery = $profile->getSQLStatement();
                }
                $html .= $this->_viewRenderElement('SQL', $profile->getSQLStatement());
                $html .= $this->_viewRenderElement(
                    'Time',
                    round($profile->getTotalElapsedSeconds() * 1000, 4) . ' ms<br/>',
                    null,
                    true
                );
            }

            $html = sprintf($html, $longestQuery, round($longestQueryTime * 1000, 4));
        }

        return $html;
    }

    /**
     * Get html for errors section.
     *
     * @param array $errorsData  Errors data.
     * @param int   $errorsCount Errors count.
     *
     * @return string
     */
    private function _getHtmlErrors($errorsData, $errorsCount)
    {
        $html = ($errorsCount == 0 ? 'No Errors' : '');
        foreach ($errorsData as $data) {
            $html .= $this->_viewRenderElement($data['error'], str_replace('#', '<br/>#', $data['trace']));
        }

        return $html;
    }
}
