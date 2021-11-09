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

namespace crmeb\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;

class Update extends Command
{

    protected function configure()
    {
        $this->setName('chat:update')->setDescription('命令行更新');
    }

    protected function execute(Input $input, Output $output)
    {

    }
}
