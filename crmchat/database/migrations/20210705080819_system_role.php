<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemRole extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('system_role', ['comment' => '身份管理表']);
        $table->addColumn('role_name', 'string', ['limit' => 32, 'default' => '', 'comment' => '身份管理名称']);
        $table->addColumn('rules', 'text', ['default' => '', 'comment' => '身份管理权限(menus_id)']);
        $table->addColumn('level', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '身份等级']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '状态']);
        $table->create();
    }
}
