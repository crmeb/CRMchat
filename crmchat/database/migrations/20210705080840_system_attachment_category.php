<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemAttachmentCategory extends Migrator
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
        $table = $this->table('system_attachment_category', ['comment' => '附件分类表']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '父级ID']);
        $table->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '分类名称']);
        $table->addColumn('enname', 'string', ['limit' => 50, 'default' => '', 'comment' => '分类目录']);
        $table->create();
    }
}
