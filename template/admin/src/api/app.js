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
 * @description  图文管理 -- 列表
 * @param {Object} param data {Object} 传值参数
 */
export function wechatNewsListApi(params) {
    return request({
        url: `app/wechat/news`,
        method: 'GET',
        params
    })
}





/**
 * @description  微信用户 -- 列表
 * @param {Object} param params {Object} 传值参数
 */
export function wechatUserListtApi(params) {
    return request({
        url: `app/wechat/user`,
        method: 'GET',
        params
    })
};

/**
 * @description  微信用户 -- 用户分组和标签
 */
export function tagListtApi() {
    return request({
        url: `app/wechat/user/tag_group`,
        method: 'GET'
    })
};

/**
 * @description  微信用户 -- 用户分组和标签编辑
 * @param {String} param url {String} 请求地址
 */
export function groupsEditApi(url) {
    return request({
        url: url,
        method: 'GET'
    })
};

/**
 * @description  用户标签 -- 列表
 */
export function wechatTagListApi() {
    return request({
        url: `app/wechat/tag`,
        method: 'GET'
    })
};

/**
 * @description  用户标签 -- 添加表单
 */
export function wechatTagCreateApi() {
    return request({
        url: `app/wechat/tag/create`,
        method: 'GET'
    })
};

/**
 * @description  用户标签 -- 编辑表单
 *  @param {Number} param id {Number} 标签id
 */
export function wechatTagEditApi(id) {
    return request({
        url: `app/wechat/tag/${id}/edit`,
        method: 'GET'
    })
};

/**
 * @description  用户分组 -- 列表
 */
export function wechatGroupListApi() {
    return request({
        url: `app/wechat/group`,
        method: 'GET'
    })
};

/**
 * @description  用户分组 -- 添加表单
 */
export function wechatGroupCreateApi() {
    return request({
        url: `app/wechat/group/create`,
        method: 'GET'
    })
};

/**
 * @description  用户分组 -- 编辑表单
 *  @param {Number} param id {Number} 标签id
 */
export function wechatGroupEditApi(id) {
    return request({
        url: `app/wechat/group/${id}/edit`,
        method: 'GET'
    })
};

/**
 * @description  用户行为 -- 列表
 */
export function wechatActionListApi(params) {
    return request({
        url: `app/wechat/action`,
        method: 'GET',
        params
    })
};

