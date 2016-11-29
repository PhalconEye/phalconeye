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

namespace Core\Plugin;

use Core\Api\I18nApi;
use Core\Model\LanguageModel;
use Engine\Dispatcher;
use Engine\Exception as EngineException;
use Phalcon\Events\Event;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\User\Plugin as PhalconPlugin;

/**
 * Dispatch plugin.
 *
 * @category  PhalconEye
 * @package   Engine\Plugin
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class DispatchPlugin extends PhalconPlugin
{
    const LANGUAGE_PATTERN = '/^\/[a-z]{2}(?:\/|$)/';

    /**
     * Catch event beforeDispatchLoop and check language in url.
     *
     * @param Event $event Dispatch event.
     *
     * @return bool
     */
    public function beforeDispatchLoop(Event $event)
    {
        if (!$this->getDI()->getConfig()->application->languages->languageInUrl) {
            return true;
        }

        /** @var Dispatcher $dispatcher */
        /** @var Router $router */
        /** @var I18nApi $i18nApi */
        $i18nApi = $this->getDI()->get('Core')->i18n();
        $dispatcher = $event->getSource();
        $router = $this->getDI()->getRouter();
        $url = $router->getRewriteUri();
        $urlLanguage = $this->_getLanguage($url);

        if ($urlLanguage == null) {
            $this->response->redirect(ltrim($url, '/'));
            return false;
        }

        if (LanguageModel::count(['language = ?0', 'bind' => [$urlLanguage]]) == 0) {
            $url = substr($url, 4, mb_strlen($url));
            $this->response->redirect($url);
            return false;
        }

        $router->clear();
        $router->handle(substr($url, 3, mb_strlen($url)));
        $dispatcher->setNamespaceName($router->getNamespaceName());
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());
        $dispatcher->setParam('language', $urlLanguage);
        $i18nApi->setLanguage($urlLanguage);
    }

    /**
     * Get language from url.
     *
     * @param string $url Url with language: /en/some/page.
     *
     * @return mixed|null
     */
    private function _getLanguage(string $url)
    {
        $matches = [];
        preg_match(self::LANGUAGE_PATTERN, $url, $matches);

        if (count($matches) == 0) {
            return null;
        }

        return str_replace('/', '', $matches[0]);
    }
}