<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemGroup extends Migrator
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
        $table = $this->table('system_group', ['comment' => '配置分类表']);
        $table->addColumn('cate_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '分类id']);
        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '数据组名称']);
        $table->addColumn('info', 'string', ['limit' => 255, 'default' => '', 'comment' => '数据提示']);
        $table->addColumn('config_name', 'string', ['limit' => 50, 'default' => '', 'comment' => '数据字段']);
        $table->addColumn('fields', 'text', ['comment' => '数据组字段以及类型（json数据）']);
        $table->addIndex('cate_id', ['name' => 'cate_id']);
        $table->create();
    }
}
