// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from '@/libs/request'




/**
 * @description 已发布管理--列表
 * @param {Object} param params {Object} 传值参数
 */
export function releasedListApi(params) {
    return request({
        url: 'marketing/coupon/released',
        method: 'get',
        params
    })
}









