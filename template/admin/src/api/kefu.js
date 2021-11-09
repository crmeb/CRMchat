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

export function getKefuInfo() {
  return request({
    url: '/service/info',
    method: 'get',
    kefu: true
  });
}

export function getAuthReply(params) {
  return request({
    url: '/service/auth_reply',
    method: 'get',
    params,
    kefu: true
  });
}

export function saveAuthReply(id,data) {
  return request({
    url: '/service/auth_reply/'+id,
    method: 'post',
    data,
    kefu: true
  });
}

export function updateAuthReply(value) {
  return request({
    url: '/service/auth_reply/'+value,
    method: 'put',
    kefu: true
  });
}

/*
 * 登录
 * */
export function AccountLogin(data) {
  return request({
    url: '/login',
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 获取左侧客服聊天用户列表
 * @constructor
 */
export function record(params) {
  return request({
    url: '/user/record',
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 获取左侧用户详情
 * @constructor
 */
export function userInfo(id) {
  return request({
    url: '/user/info/' + id,
    method: 'get',
    kefu: true
  });
}

/**
 * 获取左侧用户订单列表
 * @constructor
 */
export function getorderList(id, params) {
  return request({
    url: '/order/list/' + id,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客服订单发货
 * @constructor
 */
export function orderDelivery(id, data) {
  return request({
    url: '/order/delivery/' + id,
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 一键改价
 */
export function editPriceApi(id, data) {
  return request({
    url: `/order/update/${id}`,
    method: 'put',
    data,
    kefu: true
  })
}

/**
 * 客服订单改价
 * @constructor
 */
export function orderEdit(id) {
  return request({
    url: 'order/edit/' + id,
    method: 'get',
    kefu: true
  });
}

/**
 * 客服订单退款表单
 * @constructor
 */
export function orderRecord(id) {
  return request({
    url: 'order/refund_form/' + id,
    method: 'get',
    kefu: true
  });
}

/**
 * 客服订单退款
 * @constructor
 */
export function orderRefundApi(data) {
  return request({
    url: 'order/refund',
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 商品购买记录
 * @constructor
 */
// export function productCart(uid, params) {
//   return request({
//     url: 'product/cart/' + uid,
//     method: 'get',
//     params,
//     kefu: true
//   });
// }

/**
 * 商品足记
 * @constructor
 */
export function productVisit(uid, params) {
  return request({
    url: 'product/visit/' + uid,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 热销商品
 * @constructor
 */
export function productHot(uid, params) {
  return request({
    url: 'product/hot/' + uid,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客服话术
 * @constructor
 */
export function speeChcraft(params) {
  return request({
    url: 'service/speechcraft',
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客服转接列表
 * @constructor
 */
export function transferList(params) {
  return request({
    url: 'service/transfer_list',
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客服转接列表
 * @constructor
 */
export function serviceTransfer(params) {
  return request({
    url: 'service/transfer',
    method: 'post',
    params,
    kefu: true
  });
}

/**
 * 客服用户标签
 * @constructor
 */
export function userLabel(id) {
  return request({
    url: `user/label?id=${id}`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客服用户标签更新
 * @constructor
 */
export function userLabelPut(id, data) {
  return request({
    url: `user/label/${id}`,
    method: 'put',
    data,
    kefu: true
  });
}

/**
 * 客服用户聊天列表
 * @constructor
 */
export function serviceList(params) {
  return request({
    url: `service/list`,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 退出登录
 * @constructor
 */
export function AccountLogoutKefu() {
  return request({
    url: `user/logout`,
    method: 'post',
    kefu: true
  });
}

/**
 * 获取扫码登录凭证
 * @constructor
 */
export function getSanCodeKey() {
  return request({
    url: `/key`,
    method: 'get',
    kefu: true
  });
}

/**
 * 商品详情
 * @constructor
 */
export function productInfo(id) {
  return request({
    url: `product/info/${id}`,
    method: 'get',
    kefu: true
  });
}


/**
 * 获取轮播图和logo
 */
export function loginInfoApi() {
  return request({
    url: '/login/info',
    method: 'get',
    kefu: true
  });
}

/**
 * 订单备注
 */
export function orderRemark(data) {
  return request({
    url: '/order/remark',
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 订单详情
 */
export function orderInfo(id) {
  return request({
    url: '/order/info/' + id,
    method: 'get',
    kefu: true
  });
}

/**
 * 物流公司
 */
export function orderExport() {
  return request({
    url: '/order/export',
    method: 'get',
    kefu: true
  });
}

/**
 * 快递公司模板
 */
export function orderTemp(params) {
  return request({
    url: '/order/temp',
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 获取配送员列表
 */
export function orderDeliveryAll() {
  return request({
    url: '/order/delivery_all',
    method: 'get',
    kefu: true
  });
}

/**
 * 获取发货人员
 */
export function getSender() {
  return request({
    url: '/order/delivery_info',
    method: 'get',
    kefu: true
  });
}

/**
 * 获取话术分类
 */
export function serviceCate(params) {
  return request({
    url: '/service/cate',
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 修改话术
 */
export function serviceCateUpdate(id, params) {
  return request({
    url: 'service/speechcraft/' + id,
    method: 'PUT',
    params,
    kefu: true
  });
}

/**
 * 添加话术
 */
export function addSpeeChcraft(data) {
  return request({
    url: 'service/speechcraft',
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 添加分类
 */
export function addServiceCate(data) {
  return request({
    url: 'service/cate',
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 修改分类
 */
export function editServiceCate(id, params) {
  return request({
    url: 'service/cate/' + id,
    method: 'PUT',
    params,
    kefu: true
  });
}

/**
 * 扫码登录情况
 */
export function scanStatus(key, params) {
  return request({
    url: 'scan/' + key,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 扫码核销
 */
export function orderVerificApi(id) {
  return request({
    url: `/order/verific/${id}`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客服用户分组
 * @constructor
 */
export function userGroupApi() {
  return request({
    url: `user/group`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客服设置用户分组
 * @constructor
 */
export function putGroupApi(uid, id) {
  return request({
    url: `user/group/${uid}/${id}`,
    method: 'put',
    kefu: true
  });
}

/**
 * 客服配置
 * @constructor
 */
export function kefuConfig() {
  return request({
    url: `config`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客户端 随机客服
 * @constructor
 */
export function serviceListApi(params) {
  return request({
    url: `tourist/user`,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客户端 广告位
 * @constructor
 */
export function getAdvApi() {
  return request({
    url: `tourist/adv`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客户端 聊天记录
 * @constructor
 */
export function chatListApi(params) {
  return request({
    url: `tourist/chat`,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客户端 客服反馈
 * @constructor
 */
export function feedbackDataApi() {
  return request({
    url: `tourist/feedback`,
    method: 'get',
    kefu: true
  });
}

/**
 * 客户端 反馈提示语
 * @constructor
 */
export function feedbackFromApi(data) {
  return request({
    url: `tourist/feedback`,
    method: 'post',
    data,
    kefu: true
  });
}

/**
 * 客户端 游客获取用户订单号
 * @constructor
 */
export function getOrderApi(order_id, params) {
  return request({
    url: `tourist/order/${order_id}`,
    method: 'get',
    params,
    kefu: true
  });
}

/**
 * 客户端 商品详情
 * @constructor
 */
export function productApi(id) {
  return request({
    url: `tourist/product/${id}`,
    method: 'get',
    kefu: true
  });
}

/*
  客户端，个人中心
*/

export function kefuUserInfo() {
  return request({
    url: 'user/userInfo',
    method: 'get',
    kefu: true
  })
}

/*
  客户端，修改客服信息
*/

export function userUserInfo(data) {
  return request({
    url: 'user/userInfo',
    method: 'put',
    kefu: true,
    data
  })
}

/*
  客户端，查询客服广告
*/

export function userRecord(params) {
  return request({
    url: 'user/record',
    method: 'get',
    mobile: true,
    params
  })
}

/*
  客户端，提交反馈，get 查询并展示广告
*/

export function serviceFeedback() {
  return request({
    url: `service/feedback`,
    method: 'get',
    mobile: true
  })
}

/*
  客户端，提交用户反馈
*/
export function serviceFeedbackPost(data) {
  return request({
    url: 'service/feedback',
    method: 'post',
    mobile: true,
    data
  })
}

/*
  客户端 上传图片
*/
export function serviceUpload(data) {
  return request({
    url: 'service/upload',
    method: 'post',
    mobile: true,
    data
  })
}

/*
  客户端 获取图文信息
*/
export function serviceCache(key) {
  return request({
    url: `service/cache/${key}`,
    method: 'get',
    mobile: true
  })
}

/*
  客户端 获取token接口
*/

export function adminAppCustomer() {
  return request({
    url: '/app',
    method: 'get'
  })
}

/*
  重置token
*/

export function appReset(id) {
  return request({
    url: `/app/reset/${id}`,
    method: 'put'
  })
}

/*
  获取客服广告
*/

export function serviceAdv() {
  return request({
    url: '/service/adv',
    method: 'get',
    mobile: true
  })
}

/*
  修改用户信息
*/

export function updateUserData(userId, data) {
  return request({
    url: `user/updateUser/${userId}`,
    method: 'put',
    kefu: true,
    data
  })
}
