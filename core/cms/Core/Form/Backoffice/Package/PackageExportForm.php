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

namespace Core\Form\Backoffice\Package;

use Core\Form\CoreForm;
use Core\Model\PackageModel;
use Engine\Package\Manager;

/**
 * Export package.
 *
 * @category  PhalconEye
 * @package   Core\Form\Admin\Package
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class PackageExportForm extends CoreForm
{
    /**
     * Package object.
     *
     * @var PackageModel
     */
    protected $_package;

    /**
     * Exclude data.
     *
     * @var array|null
     */
    protected $_exclude;

    /**
     * Form constructor.
     *
     * @param PackageModel $package Package object.
     * @param null|array   $exclude Exclude data.
     */
    public function __construct($package, $exclude = null)
    {
        $this->_package = $package;
        $this->_exclude = $exclude;
        parent::__construct();
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setDescription('Select package dependency (not necessarily).');

        $content = $this->addContentFieldSet();

        if ($this->_exclude['type'] != Manager::PACKAGE_TYPE_LIBRARY) {
            $query = $this->getDI()->get('modelsManager')->createBuilder()
                ->from(['t' => '\Core\Model\Package'])
                ->where("t.type = :type:", ['type' => Manager::PACKAGE_TYPE_MODULE])
                ->andWhere("t.enabled = :enabled:", ['enabled' => true]);

            if ($this->_exclude && $this->_exclude['type'] == Manager::PACKAGE_TYPE_MODULE) {
                $query->andWhere("t.name != :name:", ['name' => $this->_exclude['name']]);
            }

            $content->addMultiSelect(
                'modules',
                'Modules',
                null,
                $query->getQuery()->execute(),
                null,
                ['using' => ['name', 'title']]
            );
        }

        $query = $this->getDI()->get('modelsManager')->createBuilder()
            ->from(['t' => '\Core\Model\Package'])
            ->where("t.type = :type:", ['type' => Manager::PACKAGE_TYPE_LIBRARY])
            ->andWhere("t.enabled = :enabled:", ['enabled' => true]);

        if ($this->_exclude && $this->_exclude['type'] == Manager::PACKAGE_TYPE_LIBRARY) {
            $query->andWhere("t.name != :name:", ['name' => $this->_exclude['name']]);
        }

        $content->addMultiSelect(
            'libraries',
            'Libraries',
            null,
            $query->getQuery()->execute(),
            null,
            ['using' => ['name', 'title']]
        );

        if ($this->_package->type == Manager::PACKAGE_TYPE_MODULE) {
            $content->addCheckbox('withTranslations', 'Export with translations', null, 1, true);
        }
    }
}