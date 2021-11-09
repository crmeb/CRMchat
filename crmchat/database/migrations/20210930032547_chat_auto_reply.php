<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatAutoReply extends Migrator
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
        $table = $this->table('chat_auto_reply', ['comment' => '自动回复表']);
        $table->addColumn('keyword', 'string', ['limit' => 10, 'default' => 0, 'comment' => '关键字']);
        $table->addColumn('content', 'string', ['limit' => 10, 'default' => 0, 'comment' => '回复内容']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户id']);
        $table->addColumn('appid', 'string', ['limit' => 35, 'default' => '', 'comment' => 'APPID']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => '', 'comment' => 排序]);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->create();
    }
}
