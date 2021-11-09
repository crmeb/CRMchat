<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemGroupData extends Migrator
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
        $table = $this->table('system_group_data', ['comment' => '组合数据详情表']);
        $table->addColumn('gid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '对应的数据组id']);
        $table->addColumn('value', 'text', ['limit' => 50, 'default' => '', 'comment' => '数据组对应的数据值（json数据）']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加数据时间']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '数据排序']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '状态（1：开启；2：关闭；）']);
        $table->addIndex('gid', ['name' => 'gid']);
        $table->create();
    }
}
