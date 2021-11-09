<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemConfigTab extends Migrator
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
        $table = $this->table('system_config_tab', ['comment' => '配置分类表']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '上级分类id']);
        $table->addColumn('title', 'string', ['limit' => 255, 'default' => '', 'comment' => '配置分类名称']);
        $table->addColumn('eng_title', 'string', ['limit' => 255, 'default' => '', 'comment' => '配置分类英文名称']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '配置分类状态']);
        $table->addColumn('info', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '配置分类是否显示']);
        $table->addColumn('icon', 'string', ['limit' => 30, 'default' => '', 'comment' => '图标']);
        $table->addColumn('type', 'integer', ['limit' => 2, 'default' => 0, 'comment' => '配置类型']);
        $table->addColumn('sort', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '排序']);
        $table->create();
    }
}
