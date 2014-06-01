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
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Engine\Db;

use Phalcon\DI;
use Phalcon\Mvc\Model as PhalconModel;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * Abstract Model.
 *
 * @category  PhalconEye
 * @package   Engine\Db
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @method static findFirstById($id)
 * @method static findFirstByLanguage($name)
 *
 * @method \Engine\Behaviour\DIBehaviour|\Phalcon\DI getDI()
 */
abstract class AbstractModel extends PhalconModel
{
    /**
     * Get table name.
     *
     * @return string
     */
    public static function getTableName()
    {
        $reader = DI::getDefault()->get('annotations');
        $reflector = $reader->get(get_called_class());
        $annotations = $reflector->getClassAnnotations();

        return $annotations->get('Source')->getArgument(0);
    }

    /**
     * Find method overload.
     * Get entities according to some condition.
     *
     * @param string      $condition Condition string.
     * @param array       $params    Condition params.
     * @param string|null $order     Order by field name.
     * @param string|null $limit     Selection limit.
     *
     * @return PhalconModel\ResultsetInterface
     */
    public static function get($condition, $params, $order = null, $limit = null)
    {
        $condition = vsprintf($condition, $params);
        $parameters = [$condition];

        if ($order) {
            $parameters['order'] = $order;
        }

        if ($limit) {
            $parameters['limit'] = $limit;
        }

        return self::find($parameters);
    }

    /**
     * FindFirst method overload.
     * Get entity according to some condition.
     *
     * @param string      $condition Condition string.
     * @param array       $params    Condition params.
     * @param string|null $order     Order by field name.
     *
     * @return AbstractModel
     */
    public static function getFirst($condition, $params, $order = null)
    {
        $condition = vsprintf($condition, $params);
        $parameters = [$condition];

        if ($order) {
            $parameters['order'] = $order;
        }

        return self::findFirst($parameters);
    }

    /**
     * Get builder associated with table of this model.
     *
     * @param string|null $tableAlias Table alias to use in query.
     *
     * @return Builder
     */
    public static function getBuilder($tableAlias = null)
    {
        $builder = new Builder();
        $table = get_called_class();
        if (!$tableAlias) {
            $builder->from($table);
        } else {
            $builder->addFrom($table, $tableAlias);
        }

        return $builder;
    }

    /**
     * Get identity.
     *
     * @return mixed
     */
    public function getId()
    {
        if (property_exists($this, 'id')) {
            return $this->id;
        }

        $primaryKeys = $this->getDI()->get('modelsMetadata')->getPrimaryKeyAttributes($this);

        switch (count($primaryKeys)) {
            case 0:
                return null;
                break;
            case 1:
                return $this->{$primaryKeys[0]};
                break;
            default:
                return array_intersect_key(
                    get_object_vars($this),
                    array_flip($primaryKeys)
                );
        }
    }
}
