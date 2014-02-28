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

namespace Core\Model;

use Engine\Package\Manager;
use Engine\Package\Model\AbstractPackage;

/**
 * Package.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("packages")
 * @HasMany("id", '\Core\Model\PackageDependency', "package_id", {
 *  "alias": "PackageDependency"
 * })
 * @HasMany("id", '\Core\Model\PackageDependency', "dependency_id", {
 *  "alias": "RelatedPackages"
 * })
 */
class Package extends AbstractPackage
{
    /**
     * Temporary dependencies data.
     *
     * @var array
     */
    protected $_dependenciesData = [];

    /**
     * Set dependencies data.
     *
     * @param array $data Dependencies list.
     *
     * @return void
     */
    public function setDependencies($data)
    {
        $this->_dependenciesData = $data;
    }

    /**
     * Return package as string, package metadata.
     *
     * @param array $params Some additional params.
     *
     * @return string
     */
    public function toJson(array $params = [])
    {
        $data = $this->getDefaultMetadata();

        // Get widgets data if this package is module.
        if ($this->type == Manager::PACKAGE_TYPE_MODULE) {
            /**
             * Widgets data.
             */
            $widgets = Widget::findByModule($this->name);
            foreach ($widgets as $widget) {
                $data['widgets'][] = [
                    'name' => $widget->name,
                    'module' => $this->name,
                    'description' => $widget->description,
                    'is_paginated' => $widget->is_paginated,
                    'is_acl_controlled' => $widget->is_acl_controlled,
                    'admin_form' => $widget->admin_form,
                    'enabled' => (bool)$widget->enabled
                ];
            }

            /**
             * Translations data.
             */
            if (!empty($params['withTranslations'])) {
                foreach (Language::find() as $language) {
                    $translations = $language->toTranslationsArray([$this->name]);
                    if (!empty($translations['content'])) {
                        $data['i18n'][] = $translations;
                    }
                }
            }
        } else {
            unset($data['widgets']);
        }

        // Check widget module.
        $packageData = $this->getData();
        if (!empty($packageData['module'])) {
            $data['module'] = $packageData['module'];
        }

        // Get events.
        if ($this->type == Manager::PACKAGE_TYPE_MODULE || $this->type == Manager::PACKAGE_TYPE_PLUGIN) {
            $packageData = $this->getData();
            if (!empty($packageData) && !empty($packageData['events'])) {
                $data['events'] = $packageData['events'];
            }
        }

        // Check dependencies.
        if (!empty($this->_dependenciesData)) {
            $data['dependencies'] = $this->_dependenciesData;
        } else {
            unset($data['dependencies']);
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Get data from json.
     *
     * @param string $content Package data in json format.
     *
     * @return void
     */
    public function fromJson($content)
    {
        $data = json_decode($content, true);
        $this->assign($data);
    }

    /**
     * Get widget object.
     *
     * @return Widget||null
     */
    public function getWidget()
    {
        if ($this->type !== Manager::PACKAGE_TYPE_WIDGET) {
            return null;
        }

        $data = $this->getData();
        if (empty($data['widget_id'])) {
            return null;
        }
        return Widget::findFirstById($data['widget_id']);
    }
}