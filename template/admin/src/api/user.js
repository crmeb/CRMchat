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


export function getAuthReply(params) {
    return request({
        url:'chat/reply',
        method:'get',
        params
    })
}

export function saveAuthReply(id,data) {
    return request({
        url:'chat/reply',
        method:'post',
        data
    })
}
export function getAuthReplyForm(id,params) {
    return request({
        url:'chat/reply/'+id,
        method:'get',
        params
    })
}
export function deleteAuthReply(id) {
    return request({
        url:'chat/reply/'+id,
        method:'delete',
    })
}

/**
 * @description 用户管理--列表
 * @param {Object} param data {Object} 传值参数
 */
export function userList(data) {
    return request({
        url: 'user/index',
        method: 'get',
        params: data
    })
}

/**
 *
 */
export function getUserLabelAllApi() {
    return request({
        url: 'user/user_label',
        method: 'get',
    })
}

/**
 * @description 编辑表单数据
 * @param {Number} param id {Number} 会员id
 */
export function getUserData(id) {
    return request({
        url: `user/edit/${id}`,
        method: 'get'
    })
}

/**
 * @description 开关
 * @param {Number} param id {Number}
 */
export function memberCard(data) {
    return request({
        url: `user/member_ship/set_ship_status`,
        method: 'get',
        params: data
    })
}

/**
 * @description 会员列表开关
 * @param {Number} param id {Number}
 */
export function memberCardStatus(data) {
    return request({
        url: `user/member_card/set_status`,
        method: 'get',
        params: data
    })
}

/**
 * @description 客服管理修改显示
 * @param {Object} param data {Object} 传入的状态值，用户id
 */
export function isShowApi(data) {
    return request({
        url: `chat/kefu/set_status/${data.id}/${data.status}`,
        method: 'put'
    })
}

/**
 * @description 会员等级任务-任务是否达成
 * @param {Number} param data.id {Number} 会员等级任务id
 * @param {Number} param data.is_must {Number} 会员等级任务是否务必达成
 */
export function setTaskMustApi(data) {
    return request({
        url: `user/user_level/set_task_must/${data.id}/${data.is_must}`,
        method: 'PUT'
    })
}

/**
 * @description 会员等级任务-新建表单 编辑表单
 * @param {Object} param data {Object} 会员等级任务对象传值
 */
export function createTaskApi(data) {
    return request({
        url: `/user/user_level/create_task`,
        method: 'get',
        params: data
    })
}

/**
 * @description 会员等级-创建表单
 * @param {Object} param data {Object} 会员等级任务对象传值
 */
export function createApi(id) {
    return request({
        url: `user/user_level/create`,
        method: 'get',
        params: id
    })
}

/**
 * @description 会员管理 --- 赠送会员等级
 * @param {Number} param id {Number} 会员id
 */
export function giveLevelApi(id) {
    return request({
        url: `user/give_level/${id}`,
        method: 'get'
    })
}

/**
 * @description 会员管理 --- 赠送会员时长
 * @param {Number} param id {Number} 会员id
 */
export function giveLevelTimeApi(id) {
    return request({
        url: `user/give_level_time/${id}`,
        method: 'get'
    })
}

/**
 * @description 会员等级-删除
 * @param {Number} param id {Number} 会员等级id
 */
export function delLevelApi(id) {
    return request({
        url: `user/user_level/delete/${id}`,
        method: 'PUT'
    })
}

/**
 * @description 分组-列表
 * @param {Object} param data {Object} 传值参数
 */
export function userGroupApi(data) {
    return request({
        url: 'user/group',
        method: 'get',
        params: data
    })
}

/**
 * @description 分组删除 --- 删除分组
 * @param {Number} param id {Number} 会员id
 */
export function groupDelApi(id) {
    return request({
        url: `user/group/${id}`,
        method: 'DELETE'
    })
}

/**
 * @description 添加分组--- 表单
 */
export function groupAddApi() {
    return request({
        url: `user/group/create`,
        method: 'get'
    })
}

/**
 * @description 修改分组--- 表单
 * @param {Number} param id {Number} 分组id
 */
export function groupSaveApi(id) {
    return request({
        url: `user/group/${id}/edit`,
        method: 'get'
    })
}

/**
 * @description 个人中心 --- 修改密码
 * data 请求参数
 */
export function updtaeAdmin(data) {
    return request({
        url: `setting/update_admin`,
        method: 'PUT',
        data
    })
}

/**
 * @description 批量设置分组
 * data 请求参数
 */
export function userSetGroup(data, method) {
    return request({
        url: `user/group/all`,
        method: method ? method : 'get',
        data
    })
}

/**
 * @description 批量设置标签
 * data 请求参数
 */
export function userLabelApi(data) {
    return request({
        url: `user/label`,
        method: 'get',
        params: data
    })
}

/**
 * @description 获取标签分类（全部）
 * data 请求参数
 */
export function userLabelAll(data) {
    return request({
        url: `user/label/cate`,
        method: 'get',
        params: data
    });
}

/**
 * 添加用户
 */
export function getUserSaveForm() {
    return request({
        url: `/user/user/create`,
        method: 'get'
    });
}

/**
 * 同步用户
 */
export function userSynchro() {
    return request({
        url: `/user/user/syncUsers`,
        method: 'get'
    });
}

/**
 * @description 获取用户标签分类编辑表单
 * data 请求参数
 */
export function userLabelEdit(id) {
    return request({
        url: `user/label/cate/${id}/edit`,
        method: 'get'
    });
}

/**
 * @description 获取用户标签分类创建表单
 * data 请求参数
 */
export function userLabelCreate(id) {
    return request({
        url: `user/label/cate/create`,
        method: 'get'
    });
}

/**
 * @description 标签表单
 * data 请求参数
 */
export function userLabelAddApi() {
    return request({
        url: `user/label/create`,
        method: 'get'
    })
}

/**
 * @description 获取修改标签表单
 * data 请求参数
 */
export function userUpdateApi(id) {
    return request({
        url: `user/label/${id}/edit`,
        method: 'get'
    })
}

/**
 * @description 标签表单
 * data 请求参数
 */
export function userSetLabelApi(data) {
    return request({
        url: `user/label/all`,
        method: 'get',
        data
    })
}

/**
 * @description 标签表单
 * data 请求参数
 */
export function userBatchLabelApi(data) {
    return request({
        url: `user/batch/label`,
        method: 'put',
        data
    })
}

/**
 * @description 获取所有分组
 * data 请求参数
 */
export function userGroupAllApi(data) {
    return request({
        url: `user/group/all`,
        method: 'get',
        data
    })
}

/**
 * @description 批量修改用户分组
 * data 请求参数
 */
export function userBatchGroupApi(data) {
    return request({
        url: `user/batch/group`,
        method: 'put',
        data
    })
}

/**
 * 批次卡列表
 */
export function userMemberBatch(data) {
    return request({
        url: '/user/member_batch/index',
        method: 'get',
        params: data
    });
}

/**
 * 生成批次卡
 * @param {*} id id
 */
export function memberBatchSave(id, data) {
    return request({
        url: `/user/member_batch/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 列表操作（启用，名称修改）
 * @param {*} id id
 */
export function memberBatchSetValue(id, data) {
    return request({
        url: `/user/member_batch/set_value/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * 会员卡列表
 * @param {*} id id
 */
export function userMemberCard(id, data) {
    return request({
        url: `/user/member_card/index/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * 会员卡导出
 * @param {*} id id
 */
export function exportMemberCard(id) {
    return request({
        url: `/export/memberCard/${id}`,
        method: 'get'
    });
}

/**
 * 会员类型
 */
export function userMemberShip() {
    return request({
        url: '/user/member/ship',
        method: 'get'
    });
}

/**
 * 编辑会员类型
 * @param {*} id id
 * @param {*} data data
 */
export function memberShipSave(id, data) {
    return request({
        url: `/user/member_ship/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 兑换会员卡二维码
 */
export function userMemberScan() {
    return request({
        url: '/user/member_scan',
        method: 'get'
    });
}

/**
 * 会员卡记录
 */
export function memberRecord(data) {
    return request({
        url: '/user/member/record',
        method: 'get',
        params: data
    });
}

/**
 * 会员权益
 */
export function memberRight() {
    return request({
        url: 'user/member/right',
        method: 'get'
    });
}

/**
 * 会员权益编辑
 * @param {*} data
 */
export function memberRightSave(data) {
    return request({
        url: `user/member_right/save/${data.id}`,
        method: 'post',
        data
    })
}

/**
 * 编辑会员协议
 * @param {*} id
 */
export function memberAgreementSave(id, data) {
    return request({
        url: `user/member_agreement/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 会员协议
 */
export function memberAgreement() {
    return request({
        url: `user/member/agreement`,
        method: 'get'
    });
}

/**
 * 获取用户标签
 */
export function getUserLabel(uid) {
    return request({
        url: `user/label/all`,
        method: 'get',
        params: { id: uid }
    });
}

/**
 * 设置用户标签
 */
export function putUserLabel(uid, data) {
    return request({
        url: `user/batch/label`,
        method: 'put',
        data
    });
}
