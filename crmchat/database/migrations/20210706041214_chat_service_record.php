<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatServiceRecord extends Migrator
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
        $table = $this->table('chat_service_record', ['comment' => '聊天记录']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => 'APPID']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '发送人的uid']);
        $table->addColumn('to_user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '送达人的uid']);
        $table->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户昵称']);
        $table->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '用户头像']);
        $table->addColumn('is_tourist', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否是游客']);
        $table->addColumn('online', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否在线']);
        $table->addColumn('type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '0 = pc,1=微信，2=小程序，3=H5']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间']);
        $table->addColumn('mssage_num', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '消息条数']);
        $table->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => null, 'comment' => '更新时间']);
        $table->addColumn('message', 'text', ['comment' => '消息内容']);
        $table->addColumn('message_type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '消息类型']);
        $table->addIndex('to_uid');
        $table->create();
    }
}
