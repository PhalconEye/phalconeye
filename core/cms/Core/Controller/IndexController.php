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

namespace Core\Controller;

use Core\Model\LanguageModel;

/**
 * Home controller.
 *
 * @category  PhalconEye
 * @package   Core\Controller
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @RoutePrefix("/", name="home")
 */
class IndexController extends AbstractController
{
    /**
     * Home action.
     *
     * @return void
     *
     * @Route("/", methods={"GET"}, name="home")
     */
    public function indexAction()
    {
        $this->_checkLanguage();
        $this->renderContent(null, null, 'home');
    }

    /**
     * Check language parameter.
     *
     * @return void
     */
    protected function _checkLanguage()
    {
        $language = preg_replace("/[^A-Za-z0-9?!]/", '', $this->request->get('lang', 'string'));
        if ($language && $languageObject = LanguageModel::findFirst("language = '" . $language . "'")) {
            $this->di->get('session')->set('language', $languageObject->language);
            $this->di->get('session')->set('locale', $languageObject->locale);
        }
    }
}

