<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Category extends Migrator
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
        $table = $this->table('category', ['comment' => '分类']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '上级id']);
        $table->addColumn('owner_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '所属人，0为全部']);
        $table->addColumn('name', 'string', ['limit' => 255, 'default' => '', 'comment' => '分类名称']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '排序']);
        $table->addColumn('type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '分类类型0=标签分类，1=快捷短语分类']);
        $table->addColumn('other', 'text', ['comment' => '其他参数']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->create();
    }
}
