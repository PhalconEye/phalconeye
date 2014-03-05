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

namespace Engine\Translation;

use Engine\Application;
use Engine\Behaviour\DIBehaviour;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Column as PhalconColumn;
use Phalcon\DiInterface;
use Phalcon\Translate\Adapter;
use Phalcon\Translate\AdapterInterface;
use Phalcon\Translate\Exception;

/**
 * Database translation.
 *
 * @category  PhalconEye
 * @package   Engine\Translation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Db implements AdapterInterface
{
    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    /**
     * Current language identity.
     *
     * @var int
     */
    protected $_languageId;

    /**
     * Translation model object.
     *
     * @var TranslationModelInterface
     */
    protected $_translationModel;

    /**
     * Translation constructor.
     *
     * @param DiInterface               $di         Dependency injection.
     * @param int                       $languageId Current language id.
     * @param TranslationModelInterface $model      Empty model example.
     *
     * @throws Exception
     */
    public function __construct($di, $languageId, TranslationModelInterface $model)
    {
        $this->__DIConstruct($di);
        $this->_languageId = $languageId;
        $this->_translationModel = $model;
    }

    /**
     * Returns the translation string of the given key.
     *
     * @param string $translateKey Key.
     * @param array  $placeholders Placeholders.
     *
     * @return string
     */
    public function _($translateKey, $placeholders = null)
    {
        return $this->query($translateKey, $placeholders);
    }

    /**
     * Returns the translation related to the given key.
     *
     * @param string $index        Index name (key).
     * @param array  $placeholders Placeholders.
     *
     * @return    string
     */
    public function query($index, $placeholders = null)
    {
        if (!$this->_languageId || empty($index) || strlen($index) == 1) {
            return $index;
        }

        // Cleanup.
        $index = preg_replace('~[\r\n]+~', '', $index);
        $translation = $this->_get($index);

        if (!$translation) {
            // Remember this translation.
            $translation = clone $this->_translationModel;
            $translation->setLanguageId($this->_languageId);
            $translation->setOriginal($index);
            $translation->setTranslated($index);

            // Set scope if available.
            if ($scope = $this->_getCurrentScope()) {
                $translation->setScope($scope);
            }

            $translation->save();

            return $index;
        }

        $translated = $translation->getTranslated();
        if ($placeholders == null) {
            return $translated;
        }

        if (is_array($placeholders)) {
            foreach ($placeholders as $key => $value) {
                $translated = str_replace('%' . $key . '%', $value, $translated);
            }

        }

        return $translated;
    }

    /**
     * Check whether is defined a translation key in the internal array.
     *
     * @param string $index Key name.
     *
     * @return bool
     */
    public function exists($index)
    {
        return $this->_get($index) !== null;
    }

    /**
     * Get by key.
     *
     * @param string $index Key name.
     *
     * @return TranslationModelInterface
     */
    private function _get($index)
    {
        $translationModel = get_class($this->_translationModel);
        return $translationModel::findFirst(
            [
                'conditions' => 'original = :content: AND language_id = :id:',
                'bind' => (
                    [
                        "content" => $index,
                        "id" => $this->_languageId
                    ]
                    ),
                'bindTypes' => (
                    [
                        "content" => PhalconColumn::BIND_PARAM_STR,
                        "id" => PhalconColumn::BIND_PARAM_INT
                    ]
                    )
            ]
        );
    }

    /**
     * Analyze debug backtrace and check from what application scope it was called.
     *
     * @return string|null
     */
    private function _getCurrentScope()
    {
        $trace = debug_backtrace();
        $scope = Application::SYSTEM_DEFAULT_MODULE;
        $skipScopes = ['Engine', 'Phalcon', ucfirst(Application::SYSTEM_DEFAULT_MODULE)];
        $viewSeparator = $this->getDI()->getConfig()->application->view->compiledSeparator;

        foreach ($trace as $item) {
            // First try detect by view file.
            if (isset($item['file']) && strpos($item['file'], '.volt.php') !== false) {
                $file = basename($item['file']);
                $appPath = str_replace(DS, $viewSeparator, ROOT_PATH . DS . 'app') . $viewSeparator;
                $file = str_replace($appPath, '', $file);

                $parts = explode('_', $file);
                if (isset($parts[1])) {
                    $result = ucfirst($parts[1]);
                    if (!in_array($result, $skipScopes)) {
                        $scope = strtolower($result);
                        break;
                    }

                }
            }

            // Then try detect by file class.
            if (!isset($item['class'])) {
                continue;
            }

            $parts = explode('\\', $item['class']);
            if (empty($parts)) {
                continue;
            }

            $result = $parts[0];
            if (in_array($result, $skipScopes)) {
                continue;
            }

            $scope = strtolower($result);
            break;
        }

        return $scope;
    }
}