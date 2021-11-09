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

namespace app\dao\chat;


use app\dao\other\AuxiliaryDao;

/**
 * 辅助
 * Class ChatServiceAuxiliaryDao
 * @package app\dao\chat
 */
class ChatServiceAuxiliaryDao extends AuxiliaryDao
{
    /**
     * 搜索
     * @param array $where
     * @return \crmeb\basic\BaseModel|mixed|\think\Model
     */
    protected function search(array $where = [])
    {
        return parent::search($where)->where('type', 0);
    }
}
