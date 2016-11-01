<?php
namespace Main\Migration;


use Core\Model\MenuModel;
use Engine\Migration\AbstractMigration;

class Migration_20161101_122343 extends AbstractMigration
{
    function run()
    {
        $menu = MenuModel::findFirst();
        $newMenu = new MenuModel($menu->toArray());
        $newMenu->id = null;
        $newMenu->save();
    }
}