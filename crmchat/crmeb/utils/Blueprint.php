<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace crmeb\utils;

use think\migration\db\Table;

class Blueprint
{

    /**
     * @var Table
     */
    protected $table;

    /**
     * 引擎
     * @var string
     */
    public $engine = 'InnoDB';

    /**
     * 字符集
     * @var string
     */
    public $charset = 'utf8';

    /**
     * 数据库字符集
     * @var string
     */
    public $collation = 'utf8_unicode_ci';

    /**
     * Blueprint constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function string()
    {

    }

    public function tinyInteger()
    {

    }

    public function integer(string $field, int $length = 10)
    {

    }

    public function timestamps($name = null)
    {

    }

    public function index($field, string $name = null)
    {
    }

    public function unique($field, string $name = null)
    {

    }

    public function enum($field)
    {

    }


}
