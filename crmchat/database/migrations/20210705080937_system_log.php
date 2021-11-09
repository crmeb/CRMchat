<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemLog extends Migrator
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
        $table = $this->table('system_log', ['comment' => '管理员操作记录表']);
        $table->addColumn('admin_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '管理员id']);
        $table->addColumn('admin_name', 'string', ['limit' => 64, 'default' => '', 'comment' => '管理员姓名']);
        $table->addColumn('path', 'string', ['limit' => 128, 'default' => '', 'comment' => '链接']);
        $table->addColumn('page', 'string', ['limit' => 64, 'default' => '', 'comment' => '行为']);
        $table->addColumn('method', 'string', ['limit' => 12, 'default' => '', 'comment' => '访问类型']);
        $table->addColumn('ip', 'string', ['limit' => 16, 'default' => '', 'comment' => '登录IP']);
        $table->addColumn('type', 'string', ['limit' => 32, 'default' => '', 'comment' => '类型']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '操作时间']);
        $table->addColumn('merchant_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '商户id']);
        $table->addIndex('admin_id', ['name' => 'admin_id']);
        $table->addIndex('add_time', ['name' => 'add_time']);
        $table->addIndex('type', ['name' => 'type']);
        $table->create();
    }
}
