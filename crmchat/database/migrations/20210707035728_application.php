<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Application extends Migrator
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
        $table = $this->table('application', ['comment' => '应用']);
        $table->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '应用名称']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => '应用ID']);
        $table->addColumn('app_secret', 'string', ['limit' => 255, 'default' => '', 'comment' => '应用KEY']);
        $table->addColumn('icon', 'string', ['limit' => 255, 'default' => '', 'comment' => '应用图标']);
        $table->addColumn('introduce', 'string', ['limit' => 255, 'default' => '', 'comment' => '应用介绍']);
        $table->addColumn('timestamp', 'integer', ['limit' => 10, 'default' => 0, 'comment' => 'TOKEN生成时间戳']);
        $table->addColumn('rand', 'integer', ['limit' => 4, 'default' => 0, 'comment' => 'TOKEN携带随机数']);
        $table->addColumn('token', 'string', ['limit' => 500, 'default' => '', 'comment' => 'TOKEN']);
        $table->addColumn('token_md5', 'string', ['limit' => 32, 'default' => '', 'comment' => 'TOKEN MD5']);
        $table->addColumn('is_delete', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '是否删除']);
        $table->addTimestamps();
        $table->create();
    }
}
