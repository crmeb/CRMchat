<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Auxiliary extends Migrator
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
        $table = $this->table('auxiliary', ['comment' => '辅助表']);
        $table->addColumn('binding_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '绑定id']);
        $table->addColumn('relation_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '关联id']);
        $table->addColumn('type', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '类型0=客服转接辅助，1=商品和分类辅助，2=优惠券和商品辅助']);
        $table->addColumn('other', 'string', ['limit' => 2000, 'default' => '', 'comment' => '其他参数']);
        $table->addColumn('appid', 'string', ['limit' => 35, 'default' => '', 'comment' => 'APPID']);
        $table->addColumn('status', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '数据状态 0：未执行，1：成功， 2：失败， 3:删除']);
        $table->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间']);
        $table->addColumn('add_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '添加时间']);
        $table->create();
    }
}
