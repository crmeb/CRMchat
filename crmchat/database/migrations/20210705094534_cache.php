<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Cache extends Migrator
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
        $table = $this->table('cache', ['id' => false, 'comment' => '数据缓存表']);
        $table->addColumn('key', 'string', ['limit' => 32, 'default' => '', 'comment' => '身份管理名称']);
        $table->addColumn('result', 'text', ['comment' => '缓存数据']);
        $table->addColumn('expire_time', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '失效时间0=永久']);
        $table->addColumn('add_time', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '缓存时间']);
        $table->addIndex('key');
        $table->create();
    }
}
