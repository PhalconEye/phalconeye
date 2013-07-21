<?php 

use Phalcon\Db\Column,
    Phalcon\Db\Index;

use Engine\Generator\Migrations\Model as Migration;

class MenuItemsMigration_040 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'menu_items',
            array(
            'columns' => array(
                new Column(
                    'id',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'autoIncrement' => true,
                        'size' => 11,
                        'first' => true
                    )
                ),
                new Column(
                    'title',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'id'
                    )
                ),
                new Column(
                    'menu_id',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'title'
                    )
                ),
                new Column(
                    'parent_id',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'size' => 11,
                        'after' => 'menu_id'
                    )
                ),
                new Column(
                    'page_id',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'size' => 11,
                        'after' => 'parent_id'
                    )
                ),
                new Column(
                    'url',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'page_id'
                    )
                ),
                new Column(
                    'onclick',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'url'
                    )
                ),
                new Column(
                    'target',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 10,
                        'after' => 'onclick'
                    )
                ),
                new Column(
                    'tooltip',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'target'
                    )
                ),
                new Column(
                    'tooltip_position',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 10,
                        'after' => 'tooltip'
                    )
                ),
                new Column(
                    'icon',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'tooltip_position'
                    )
                ),
                new Column(
                    'icon_position',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 10,
                        'after' => 'icon'
                    )
                ),
                new Column(
                    'item_order',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'icon_position'
                    )
                ),
                new Column(
                    'languages',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 150,
                        'after' => 'item_order'
                    )
                ),
                new Column(
                    'roles',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 150,
                        'after' => 'languages'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('id'))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '81',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_general_ci'
            )
        )
        );
    }
}
