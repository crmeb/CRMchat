// +---------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

const pre = 'kefu_';

export default [
	// 登录
	{
		path: '/admin/login',
		name: 'login',
		meta: {
			title: '登录',
			hideInMenu: true
		},
		component: () => import('@/pages/account/login')
	},
	// 客服
	{
		path: '/kefu',
		name: `${pre}index`,
		meta: {
			auth: true,
			title: '客服管理',
			kefu: true
		},
		component: () => import('@/pages/kefu/index')
	},


	{
		path: '/kefu/pc_list',
		name: `${pre}pc_list`,
		meta: {
			auth: true,
			title: '客服',
			kefu: true
		},
		component: () => import('@/pages/kefu/pc/index')
	},
	{
		path: '/kefu/appChat',
		name: `${pre}app-chat`,
		meta: {
			auth: true,
			title: '客服',
			kefu: true
		},
		component: () => import('@/pages/kefu/appChat/index')
	},
	{
		path: '/kefu/mobile_user_chat',
		name: `${pre}app-mobile_user_chat`,
		meta: {
			auth: true,
			title: '用户客服',
			kefu: true
		},
		component: () => import('@/pages/kefu/appChat/mobile/index')
	},
	{
		path: '/kefu/mobile_feedback',
		name: `${pre}app-mobile_feedback`,
		meta: {
			auth: true,
			title: '用户反馈',
			kefu: true
		},
		component: () => import('@/pages/kefu/appChat/mobile/feedback')
	},
	// 外部连接，跳转联系客服模块
	{
		path: '/chat/index',
		name: 'customerServerRedirect',
		meta: {
			title: '联系客服'
		},
		component: () => import('@/pages/kefu/externalConnection/index')
	},
	{
		path: '/chat/pc',
		name: 'customerServerPc',
		meta: {
			title: '联系客服pc'
		},
		component: () => import('@/pages/kefu/externalConnection/pcCustomerServer')
	},
	{
		path: '/chat/mobile',
		name: 'customerServerMobile',
		meta: {
			title: '联系客服mobile'
		},
		component: () => import('@/pages/kefu/externalConnection/mobileCustomerServer')
	},
	{
		// 客服不在线。提交反馈
		path: '/chat/customerOutLine',
		name: 'customerOutLine',
		meta: {
			title: '提交反馈'
		},
		component: () => import('@/pages/kefu/externalConnection/customerOutLine')
	},
	{
		// 完成提交反馈
		path: '/chat/finishSubmitOutLine',
		name: 'finishSubmitOutLine',
		meta: {
			title: '提交成功'
		},
		component: () => import('@/pages/kefu/externalConnection/finishSubmitOutLine')
	}
	// 外部连接，跳转联系客服模块结束
]
