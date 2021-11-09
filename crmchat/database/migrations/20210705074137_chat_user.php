<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatUser extends Migrator
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
        $table = $this->table('chat_user', ['comment' => '客服用户']);
        $table->addColumn('uid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户UID']);
        $table->addColumn('group_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户分组']);
        $table->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户昵称']);
        $table->addColumn('remark_nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '备注用户昵称']);
        $table->addColumn('openid', 'string', ['limit' => 50, 'default' => '', 'comment' => 'openid']);
        $table->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像']);
        $table->addColumn('phone', 'string', ['limit' => 11, 'default' => '', 'comment' => '手机号']);
        $table->addColumn('last_ip', 'string', ['limit' => 16, 'default' => '', 'comment' => '访问ip']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => 'appid']);
        $table->addColumn('remarks', 'string', ['limit' => 255, 'default' => '', 'comment' => '备注']);
        $table->addColumn('is_delete', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否删除']);
        $table->addColumn('is_kefu', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否客服']);
        $table->addColumn('is_tourist', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否游客']);
        $table->addColumn('type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '用户类型 0 = pc , 1 = 微信 ，2 = 小程序 ，3 = H5, 4 = APP']);
        $table->addColumn('sex', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '0=未知,1=男,2=女']);
        $table->addColumn('online', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '1=在线,0=下线']);
        $table->addColumn('version', 'string', ['limit' => 32, 'default' => '', 'comment' => '版本号']);
        $table->addTimestamps();
        $table->create();
    }
}
