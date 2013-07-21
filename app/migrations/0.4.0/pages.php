<?php 

use Phalcon\Db\Column,
    Phalcon\Db\Index;

use Engine\Generator\Migrations\Model as Migration;

class PagesMigration_040 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'pages',
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
                    'type',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 25,
                        'after' => 'title'
                    )
                ),
                new Column(
                    'url',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 255,
                        'after' => 'type'
                    )
                ),
                new Column(
                    'description',
                    array(
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'url'
                    )
                ),
                new Column(
                    'keywords',
                    array(
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'description'
                    )
                ),
                new Column(
                    'layout',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 50,
                        'after' => 'keywords'
                    )
                ),
                new Column(
                    'controller',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 50,
                        'after' => 'layout'
                    )
                ),
                new Column(
                    'roles',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 150,
                        'after' => 'controller'
                    )
                ),
                new Column(
                    'view_count',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'size' => 11,
                        'after' => 'roles'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('id')),
                new Index('controller', array('controller')),
                new Index('url', array('url'))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '10',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_general_ci'
            )
        )
        );
    }
}
