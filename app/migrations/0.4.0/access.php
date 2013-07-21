<?php 

use Phalcon\Db\Column,
    Phalcon\Db\Index;

use Engine\Generator\Migrations\Model as Migration;

class AccessMigration_040 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'access',
            array(
            'columns' => array(
                new Column(
                    'object',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 55,
                        'first' => true
                    )
                ),
                new Column(
                    'action',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 255,
                        'after' => 'object'
                    )
                ),
                new Column(
                    'role_id',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 11,
                        'after' => 'action'
                    )
                ),
                new Column(
                    'value',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'size' => 25,
                        'after' => 'role_id'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('object', 'action', 'role_id'))
            ),
            'options' => array(
                'TABLE_TYPE' => 'BASE TABLE',
                'AUTO_INCREMENT' => '',
                'ENGINE' => 'InnoDB',
                'TABLE_COLLATION' => 'utf8_general_ci'
            )
        )
        );
    }
}
