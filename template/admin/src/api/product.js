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


/*
 * 获取商品表单头数量；
 * */
export function getGoodsCategory(data) {
    return request({
        url: '/goods/goods_category',
        method: 'get',
        params: data
    })
}



/**
 * @description 商品管理-- 分类
 */
export function treeListApi(type) {
    return request({
        url: `product/category/tree/${type}`,
        method: 'get'
    })
}



/**
 * @description 商品分类 -- 修改状态
 * @param {Object} param params {Object} 传值参数
 */
export function setShowApi(data) {
    return request({
        url: `product/category/set_show/${data.id}/${data.is_show}`,
        method: 'PUT'
    })
}

/**
 * @description 选择商品 -- 列表
 */
export function changeListApi(params) {
    return request({
        url: `product/product/list`,
        method: 'GET',
        params
    })
}
/**
 * @description 商品 -- 获取运费模板
 */
export function productGetTemplateApi() {
    return request({
        url: `product/product/get_template`,
        method: 'get'
    })
}

/**
 * @description 商品 -- 获取运费模板
 */
export function productGetTempKeysApi() {
    return request({
        url: `product/product/get_temp_keys`,
        method: 'get'
    })
}



