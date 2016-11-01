<?php

use Core\Model\LanguageModel;
use Engine\Migration\AbstractMigration;

class Migration_20161101_132703 extends AbstractMigration
{
    function run()
    {
        // TODO: Implement run() method.
        $table = LanguageModel::getTableName();
        $this->execute("INSERT IGNORE INTO `{$table}` (name, language, locale) VALUES ('test', 'ru', 'ru_RU')");
    }
}