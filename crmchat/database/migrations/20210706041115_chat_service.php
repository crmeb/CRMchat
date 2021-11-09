<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatService extends Migrator
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
        $table = $this->table('chat_service', ['comment' => '客服表']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => 'APPID']);
        $table->addColumn('mer_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '商户id']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户自增ID']);
        $table->addColumn('online', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否在线']);
        $table->addColumn('account', 'string', ['limit' => 50, 'default' => '', 'comment' => '账号']);
        $table->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码']);
        $table->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '客服头像']);
        $table->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '代理名称']);
        $table->addColumn('phone', 'string', ['limit' => 32, 'default' => '', 'comment' => '客服电话']);
        $table->addColumn('ip', 'string', ['limit' => 50, 'default' => '', 'comment' => 'IP']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '客服状态，0隐藏1显示']);
        $table->addColumn('notify', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '订单通知1开启0关闭']);
        $table->addColumn('customer', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否展示统计管理']);
        $table->addColumn('is_app', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否为APP登陆']);
        $table->addColumn('uniqid', 'string', ['limit' => 35, 'default' => '', 'comment' => '扫码登录唯一值']);
        $table->addColumn('client_id', 'string', ['limit' => 100, 'default' => '', 'comment' => 'clientID']);
        $table->addColumn('auto_reply', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '自动回复']);
        $table->addColumn('is_backstage', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否在前台运行']);
        $table->addColumn('welcome_words', 'string', ['limit' => 500, 'default' => '', 'comment' => '欢迎语']);
        $table->addIndex('account');
        $table->addIndex('phone');
        $table->create();
    }
}
