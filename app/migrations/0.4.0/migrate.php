<?php

use Engine\Generator\Migrations\Model as Migration;

class MigrateMigration_040 extends Migration
{

    public function up()
    {
        $this->runSqlFile(self::$_migrationPath . '/data.sql');
    }

}
