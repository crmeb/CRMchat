<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemMenus extends Migrator
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
        $table = $this->table('system_menus', ['comment' => '菜单表']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '父级id']);
        $table->addColumn('icon', 'string', ['limit' => 16, 'default' => '', 'comment' => '图标']);
        $table->addColumn('menu_name', 'string', ['limit' => 32, 'default' => '', 'comment' => '按钮名']);
        $table->addColumn('module', 'string', ['limit' => 32, 'default' => '', 'comment' => '模块名']);
        $table->addColumn('controller', 'string', ['limit' => 64, 'default' => '', 'comment' => '控制器']);
        $table->addColumn('action', 'string', ['limit' => 32, 'default' => '', 'comment' => '方法名']);
        $table->addColumn('api_url', 'string', ['limit' => 100, 'default' => '', 'comment' => 'api接口地址']);
        $table->addColumn('methods', 'string', ['limit' => 255, 'default' => '', 'comment' => '提交方式POST GET PUT DELETE']);
        $table->addColumn('params', 'string', ['limit' => 128, 'default' => '', 'comment' => '参数']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '排序']);
        $table->addColumn('is_show', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否为隐藏菜单0=隐藏菜单,1=显示菜单']);
        $table->addColumn('is_show_path', 'boolean', ['limit' => 10, 'default' => 0, 'comment' => '是否为隐藏菜单供前台使用']);
        $table->addColumn('access', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '子管理员是否可用']);
        $table->addColumn('menu_path', 'string', ['limit' => 255, 'default' => '', 'comment' => '路由名称 前端使用']);
        $table->addColumn('path', 'string', ['limit' => 255, 'default' => '', 'comment' => '路径']);
        $table->addColumn('auth_type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否为菜单 1菜单 2功能']);
        $table->addColumn('header', 'string', ['limit' => 10, 'default' => '', 'comment' => '顶部菜单标示']);
        $table->addColumn('is_header', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否顶部菜单1是0否']);
        $table->addColumn('unique_auth', 'string', ['limit' => 255, 'default' => '', 'comment' => '前台唯一标识']);
        $table->addColumn('is_del', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否删除']);
        $table->addIndex('pid');
        $table->addIndex('is_show');
        $table->addIndex('access');
        $table->create();
    }
}
