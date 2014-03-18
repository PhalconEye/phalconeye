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

namespace Engine\Grid;

use Engine\Behaviour\DIBehaviour;
use Engine\Behaviour\TranslationBehaviour;
use Engine\Db\AbstractModel;
use Engine\Exception;
use Engine\Form;
use Engine\Grid\Behaviour\ColumnBehaviour;
use Engine\Grid\Source\BuilderResolver;
use Engine\Grid\Source\ResolverInterface;
use Phalcon\DI;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ViewInterface;

/**
 * Abstract grid.
 *
 * @category  PhalconEye
 * @package   Engine\Grid
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
abstract class AbstractGrid implements GridInterface
{
    const
        /**
         *  Grid column label.
         */
        COLUMN_PARAM_LABEL = 'label',

        /**
         * Grid column sortable flag.
         */
        COLUMN_PARAM_SORTABLE = 'sortable',

        /**
         * Grid column DB type.
         */
        COLUMN_PARAM_TYPE = 'type',

        /**
         * Grid column filter control (type of \Engine\Form\AbstractElement).
         */
        COLUMN_PARAM_FILTER = 'filter',

        /**
         * Grid column flag: use 'HAVING' in filter instead of 'WHERE' for query builder.
         */
        COLUMN_PARAM_USE_HAVING = 'use_having',

        /**
         * Grid column filter condition 'LIKE'
         */
        COLUMN_PARAM_USE_LIKE = 'condition_like',

        /**
         * This can be a closure that will define output logic of value in this column.
         */
        COLUMN_PARAM_OUTPUT_LOGIC = 'output_logic';


    use DIBehaviour {
        DIBehaviour::__construct as protected __DIConstruct;
    }

    use ColumnBehaviour,
        TranslationBehaviour;

    /**
     * View object.
     *
     * @var View
     */
    protected $_view;

    /**
     * Response object.
     *
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * Paginator.
     *
     * @var \stdClass
     */
    protected $_paginator;

    /**
     * Create grid.
     *
     * @param ViewInterface $view View object.
     * @param DIBehaviour   $di   DI object.
     */
    public function __construct(ViewInterface $view, $di = null)
    {
        $this->__DIConstruct($di);
        $this->_view = $view;
        $this->_view->grid = $this;

        /**
         * Prepare source data.
         */
        $paginator = $this->getSourceResolver()->resolve($this->getSource());
        $this->_paginator = $paginator->getPaginate();

        /**
         * Ajax call, we need to render only partials.
         */
        if ($this->getDI()->getRequest()->isAjax()) {
            $view->disable();
            $this->_response = $this->getDI()->getResponse();
            $this->_response->setContent($this->render($this->getTableBodyView()));
        }
    }

    /**
     * Get grid identity.
     *
     * @return string
     */
    public function getId()
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', get_class($this), $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }

    /**
     * Get item action (Edit, Delete, etc).
     *
     * @param array|\Engine\Db\AbstractModel|\Engine\Grid\GridItem|\Phalcon\Mvc\Model\Row $item One item object.
     *
     * @return array
     */
    public function getItemActions(GridItem $item)
    {
        if ($item instanceof AbstractModel && method_exists($item, 'getGridActions')) {
            return $item->getGridActions();
        }

        return [];
    }

    /**
     * Returns response object if grid has something to say =)... (has it's own response).
     *
     * @return null|ResponseInterface
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Grid has actions?
     *
     * @return bool
     */
    public function hasActions()
    {
        return true;
    }

    /**
     * Grid has filter form?
     *
     * @return bool
     */
    public function hasFilterForm()
    {
        return true;
    }

    /**
     * Get items count per page.
     *
     * @return int
     */
    public function getItemsCountPerPage()
    {
        return 25;
    }

    /**
     * Get total items count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_paginator->total_items;
    }

    /**
     * Get current grid items.
     *
     * @return AbstractModel[]
     */
    public function getItems()
    {
        $items = [];
        foreach ($this->_paginator->items as $item) {
            $items[] = new GridItem($this, $item);
        }
        return $items;
    }

    /**
     * Get router name.
     *
     * @return string
     */
    public function getRoute()
    {
        $route = $this->getDI()->getRouter()->getMatchedRoute();
        if ($route) {
            return $route->getName();
        }

        return '';
    }

    /**
     * Get source resolver.
     *
     * @return ResolverInterface
     */
    public function getSourceResolver()
    {
        return new BuilderResolver($this);
    }

    /**
     * Render grid.
     *
     * @param string $viewName Name of the view file.
     *
     * @return string
     */
    public function render($viewName = null)
    {
        if (!$viewName) {
            $viewName = $this->getLayoutView();
        }

        /** @var View $view */
        $view = $this->getDI()->get('view');
        ob_start();
        $view->partial($viewName, ['grid' => $this, 'paginator' => $this->_paginator]);
        $html = ob_get_contents();
        ob_end_clean();


        if ($this->getDI()->getRequest()->isAjax()) {
            $view->setContent($html);
        }

        return $html;
    }

    /**
     * Get request param.
     *
     * @param string $name    Param name.
     * @param mixed  $default Default value for param.
     *
     * @return mixed
     */
    protected function _getParam($name, $default = null)
    {
        return $this->getDI()->getRequest()->get($name, null, $default);
    }
}