<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ChatServiceFeeback extends Migrator
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
        $table = $this->table('chat_service_feeback', ['comment' => '客服反馈表']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '用户uid']);
        $table->addColumn('rela_name', 'string', ['limit' => 50, 'default' => 0, 'comment' => '姓名']);
        $table->addColumn('phone', 'string', ['limit' => 11, 'default' => 0, 'comment' => '电话']);
        $table->addColumn('content', 'string', ['limit' => 500, 'default' => 0, 'comment' => '反馈内容']);
        $table->addColumn('make', 'string', ['limit' => 500, 'default' => '', 'comment' => '备注']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '状态0=未查看，1=已查看']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->addColumn('appid', 'string', ['limit' => 32, 'default' => '', 'comment' => 'APPID']);
        $table->create();
    }
}
