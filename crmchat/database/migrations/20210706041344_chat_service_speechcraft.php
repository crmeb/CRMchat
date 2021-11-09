<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatServiceSpeechcraft extends Migrator
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
        $table = $this->table('chat_service_speechcraft', ['comment' => '客服话术']);
        $table->addColumn('kefu_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '0为全局话术']);
        $table->addColumn('cate_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '0为不分类全局话术']);
        $table->addColumn('title', 'string', ['limit' => 100, 'default' => '', 'comment' => '话术标题']);
        $table->addColumn('message', 'string', ['limit' => 255, 'default' => '', 'comment' => '话术内容']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '排序']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->addIndex('kefu_id');
        $table->addIndex('cate_id');

        $table->create();
    }
}
