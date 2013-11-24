<?php 

use Engine\Generator\Migrations\Model as Migration;

use Phalcon\Db\Column;use Phalcon\Db\Index;

class SessionDataMigration_040 extends Migration
{

    public function up()
    {
        $this->morphTable(
            'session_data',
            array(
            'columns' => array(
                new Column(
                    'session_id',
                    array(
                        'type' => Column::TYPE_VARCHAR,
                        'notNull' => true,
                        'size' => 35,
                        'first' => true
                    )
                ),
                new Column(
                    'data',
                    array(
                        'type' => Column::TYPE_TEXT,
                        'notNull' => true,
                        'size' => 1,
                        'after' => 'session_id'
                    )
                ),
                new Column(
                    'creation_date',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'notNull' => true,
                        'size' => 15,
                        'after' => 'data'
                    )
                ),
                new Column(
                    'modification_date',
                    array(
                        'type' => Column::TYPE_INTEGER,
                        'size' => 15,
                        'after' => 'creation_date'
                    )
                )
            ),
            'indexes' => array(
                new Index('PRIMARY', array('session_id'))
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
