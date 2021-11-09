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
 * @description 订单数据--列表
 * @param {Object} param data {Object} 传值参数
 */
export function getOrdes(data) {
    return request({
        url: '/order/chart',
        method: 'get',
        params: data
    })
};






/**
 * 所有配送员列表
 */
export function deliveryList() {
    return request({
        url: '/order/delivery/index',
        method: 'get'
    });
}



/**
 * 列表修改账号状态
 * @param {*} data data
 */
export function orderDeliveryStatus(data) {
    return request({
        url: `/order/delivery/set_status/${data.id}/${data.status}`,
        method: 'get'
    });
}

/**
 * 编辑配送员表单
 * @param {*} id id
 */
export function orderDeliveryEdit(id) {
    return request({
        url: `/order/delivery/${id}/edit`,
        method: 'get'
    });
}

/**
 * 新增配送员表单
 */
export function orderDeliveryAdd() {
    return request({
        url: '/order/delivery/add',
        method: 'get'
    });
}



/**
 * @description 立即支付
 * @param {String} param path {String} 请求地址
 * @param {String} param method {String} 请求方式
 */
export function payOffline(path, method) {
    return request({
        url: path,
        method: method
    })
};










