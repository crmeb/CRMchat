<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemAttachment extends Migrator
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
        $table = $this->table('system_attachment', ['id' => 'att_id', 'comment' => '附件管理表']);
        $table->addColumn('name', 'string', ['limit' => 100, 'default' => '', 'comment' => '附件名称']);
        $table->addColumn('att_dir', 'string', ['limit' => 200, 'default' => '', 'comment' => '附件路径']);
        $table->addColumn('satt_dir', 'string', ['limit' => 200, 'default' => '', 'comment' => '压缩图片路径']);
        $table->addColumn('att_size', 'string', ['limit' => 30, 'default' => '', 'comment' => '附件大小']);
        $table->addColumn('att_type', 'string', ['limit' => 30, 'default' => '', 'comment' => '附件类型']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '分类ID']);
        $table->addColumn('time', 'integer', ['limit' => 11, 'default' => 0, 'comment' => '上传时间']);
        $table->addColumn('image_type', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '图片上传类型 1本地 2七牛云 3OSS 4COS ']);
        $table->addColumn('module_type', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '图片上传模块类型 1 后台上传 2 用户生成']);
        $table->addColumn('real_name', 'string', ['limit' => 255, 'default' => '', 'comment' => '原始文件名']);
        $table->create();
    }
}
