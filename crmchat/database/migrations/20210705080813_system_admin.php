<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemAdmin extends Migrator
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
        $table = $this->table('system_admin', ['comment' => '后台管理员表']);
        $table->addColumn('account', 'string', ['limit' => 32, 'default' => '', 'comment' => '后台管理员账号']);
        $table->addColumn('head_pic', 'string', ['limit' => 255, 'default' => '', 'comment' => '后台管理员头像']);
        $table->addColumn('pwd', 'string', ['limit' => 100, 'default' => '', 'comment' => '后台管理员密码']);
        $table->addColumn('real_name', 'string', ['limit' => 16, 'default' => '', 'comment' => '后台管理员姓名']);
        $table->addColumn('roles', 'string', ['limit' => 128, 'default' => '', 'comment' => '后台管理员权限(menus_id)']);
        $table->addColumn('last_ip', 'string', ['limit' => 16, 'default' => '', 'comment' => '后台管理员最后一次登录ip']);
        $table->addColumn('last_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '后台管理员最后一次登录时间']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '后台管理员添加时间']);
        $table->addColumn('login_count', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '登录次数']);
        $table->addColumn('level', 'integer', ['limit' => 3, 'default' => 0, 'comment' => '后台管理员级别']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '后台管理员状态 1有效0无效']);
        $table->addColumn('is_del', 'boolean', ['limit' => 0, 'default' => 0, 'comment' => '是否删除 1有效0无效']);
        $table->addIndex(['account', 'status']);
        $table->addIndex('account');
        $table->create();
    }
}
