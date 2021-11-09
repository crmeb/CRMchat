<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatUserLabelAssist extends Migrator
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
        $table = $this->table('chat_user_label_assist', ['comment' => '用户标签辅助表']);
        $table->addColumn('label_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '标签ID']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户表自增ID']);
        $table->addIndex('user_id');
        $table->create();
    }
}
