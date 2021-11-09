<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemConfig extends Migrator
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
        $table = $this->table('system_config', ['comment' => '配置表']);
        $table->addColumn('menu_name', 'string', ['limit' => 255, 'default' => '', 'comment' => '字段名称']);
        $table->addColumn('type', 'string', ['limit' => 255, 'default' => '', 'comment' => '类型(文本框,单选按钮...)']);
        $table->addColumn('input_type', 'string', ['limit' => 20, 'default' => 'input', 'comment' => '表单类型']);
        $table->addColumn('config_tab_id', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '配置分类id']);
        $table->addColumn('parameter', 'string', ['limit' => 255, 'default' => '', 'comment' => '规则 单选框和多选框']);
        $table->addColumn('upload_type', 'boolean', ['limit' => 1, 'default' => 1, 'comment' => '上传文件格式1单图2多图3文件']);
        $table->addColumn('required', 'string', ['limit' => 255, 'default' => '', 'comment' => '规则']);
        $table->addColumn('width', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '多行文本框的宽度']);
        $table->addColumn('high', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '多行文框的高度']);
        $table->addColumn('value', 'string', ['limit' => 5000, 'default' => '', 'comment' => '默认值']);
        $table->addColumn('info', 'string', ['limit' => 255, 'default' => '', 'comment' => '配置名称']);
        $table->addColumn('desc', 'string', ['limit' => 255, 'default' => '', 'comment' => '配置简介']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '排序']);
        $table->addColumn('status', 'boolean', ['limit' => 255, 'default' => 0, 'comment' => '是否隐藏']);
        $table->addIndex(['menu_name', 'status'], ['name' => 'key_status']);
        $table->addIndex('menu_name', ['name' => 'menu_name']);
        $table->create();
    }
}
