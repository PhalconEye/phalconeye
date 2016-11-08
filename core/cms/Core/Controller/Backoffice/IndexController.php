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
  +------------------------------------------------------------------------+
*/

namespace Core\Controller\Backoffice;

/**
 * Admin Index controller.
 *
 * @category  PhalconEye
 * @package   Core\Backoffice\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/backoffice")
 */
class IndexController extends AbstractBackofficeController
{
    /**
     * Index action.
     *
     * @return void
     *
     * @Get("/", name="backoffice-home")
     */
    public function indexAction()
    {
        $this->view->setRenderLevel(1); // render only action
        $this->view->debug = $this->config->application->debug;
        $this->view->profiler = $this->config->application->profiler;
    }

    /**
     * Get data about current cpu usage.
     *
     * @return void
     *
     * @Get("/monitoring", name="backoffice-monitoring")
     */
    public function cpuAction()
    {
        $data = [
            'cpu' => $this->_getCpuUsage(),
            'memory' => $this->_getMemoryUsage()
        ];

        $this->view->disable();
        $this->response->setContent(json_encode($data))->send();
    }

    /**
     * Action for mode changing.
     *
     * @return void
     *
     * @Get("/mode", name="backoffice-mode")
     */
    public function modeAction()
    {
        $this->view->disable();
        $this->config->application->debug = (bool)$this->request->get('flag', null, true);
        $this->config->save();
        $this->_clearCache();
    }

    /**
     * Action for profiler changing.
     *
     * @return void
     *
     * @Get("/profiler", name="backoffice-profiler")
     */
    public function profilerAction()
    {
        $this->view->disable();
        $this->config->application->profiler = (bool)$this->request->get('flag', null, true);
        $this->config->save();
        $this->_clearCache();
    }

    /**
     * Action for cleaning cache.
     *
     * @return void
     *
     * @Get("/clear", name="backoffice-clear")
     */
    public function cleanAction()
    {
        $this->view->disable();
        $this->_clearCache();
        $this->flashSession->success('Cache cleared!');
        $this->response->redirect(['for' => 'backoffice-home']);
    }

    /**
     * Get current CPU usage.
     *
     * @return int
     */
    protected function _getCpuUsage()
    {
        if (stristr(PHP_OS, 'win')) {
            // @TODO: test on windows.
            $wmi = new \COM("Winmgmts://");
            $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");

            $cpu_num = 0;
            $load_total = 0;

            foreach ($server as $cpu) {
                $cpu_num++;
                $load_total += $cpu->loadpercentage;
            }

            return (int)(round($load_total / $cpu_num) * 100);
        } else {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $cpuCount = count($matches[0]);

            $sys_load = sys_getloadavg();
            return (int)($sys_load[0] / $cpuCount * 100);
        }
    }


    /**
     * Get current memory usage and total.
     *
     * @return array
     */
    protected function _getMemoryUsage()
    {
        $result = [];
        if (stristr(PHP_OS, 'win')) {
            // @TODO: implement for windows.
            $result['total'] = 0;
            $result['used'] = 0;
        } else {
            $data = explode("\n", file_get_contents("/proc/meminfo"));
            $meminfo = array();
            foreach ($data as $line) {
                if (empty($line)) {
                    continue;
                }
                list($key, $val) = explode(":", $line);
                $meminfo[$key] = (int)trim($val);
            }
            $result['total'] = (int)($meminfo['MemTotal'] / 1024);
            $result['usage'] = (int)(($meminfo['MemTotal'] - $meminfo['MemFree']) / 1024);
        }

        return $result;
    }
}

