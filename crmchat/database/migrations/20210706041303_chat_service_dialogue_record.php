<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatServiceDialogueRecord extends Migrator
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
        $table = $this->table('chat_service_dialogue_record', ['comment' => '用户和客服对话记录']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => 'APPID']);
        $table->addColumn('mer_id', 'integer', ['limit' => 32, 'default' => 0, 'comment' => '商户id']);
        $table->addColumn('msn', 'text', ['comment' => '消息内容']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '发送人uid']);
        $table->addColumn('to_user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '接收人uid']);
        $table->addColumn('is_tourist', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '1=游客模式，0=非游客']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '发送时间']);
        $table->addColumn('type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否已读（0：否；1：是；）']);
        $table->addColumn('remind', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否提醒过']);
        $table->addColumn('msn_type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '消息类型 1=文字 2=表情 3=图片 4=语音']);
        $table->addColumn('other', 'string', ['limit' => 2000, 'default' => '', 'comment' => '其他参数']);
        $table->addColumn('guid', 'string', ['limit' => 100, 'default' => '', 'comment' => 'Guid,相当于唯一值']);
        $table->addIndex(['to_uid', 'uid']);
        $table->create();
    }
}
