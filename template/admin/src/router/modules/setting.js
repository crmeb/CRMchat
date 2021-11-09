// +---------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import BasicLayout from '@/components/main'

const meta = {
  auth: true
}

const pre = 'setting_'

export default {
  path: '/admin/setting',
  name: 'setting',
  header: 'setting',
  redirect: {
    name: `${pre}systemRole`
  },
  component: BasicLayout,
  children: [
    {
      path: 'system_role/index',
      name: `${pre}systemRole`,
      meta: {
        auth: ['setting-system-role'],
        title: '身份管理'
      },
      component: () => import('@/pages/setting/systemRole/index')
    },
    {
      path: 'system_admin/index',
      name: `${pre}systemAdmin`,
      meta: {
        auth: ['setting-system-list'],
        title: '管理员列表'
      },
      component: () => import('@/pages/setting/systemAdmin/index')
    },
    {
      path: 'system_menus/index',
      name: `${pre}systemMenus`,
      meta: {
        auth: ['setting-system-menus'],
        title: '权限规则'
      },
      component: () => import('@/pages/setting/systemMenus/index')
    },
    {
      path: 'system_config',
      name: `${pre}setSystem`,
      meta: {
        auth: ['setting-system-config'],
        title: '系统设置'
      },
      component: () => import('@/pages/setting/setSystem/index')
    },
    {
      path: 'store_service/index',
      name: `${pre}service`,
      meta: {
        auth: ['setting-store-service'],
        title: '客服管理'
      },
      component: () => import('@/pages/setting/storeService/index')
    },
    {
      path: 'system_group_data',
      name: `${pre}systemGroupData`,
      meta: {
        auth: ['admin-setting-pages-links'],
        title: '数据配置'
      },
      component: () => import('@/pages/system/group/list')
    },
    {
      path: 'system_group_data/kf_adv',
      name: `${pre}kfAdv`,
      meta: {
        auth: ['setting-system-group_data-kf_adv'],
        title: '客服页面广告'
      },
      component: () => import('@/pages/system/group/kfAdv')
    },
    {
      path: 'system_group_data/privacy',
      name: `${pre}privacy`,
      meta: {
        auth: ['setting-system-group_data-privacy'],
        title: '隐私协议'
      },
      component: () => import('@/pages/system/group/privacy')
    },
    {
      path: 'store_service/speechcraft',
      name: `${pre}speechcraft`,
      meta: {
        auth: ['admin-setting-store_service-speechcraft'],
        title: '客服话术'
      },
      component: () => import('@/pages/setting/storeService/speechcraft')
    },
    {
      path: 'store_service/feedback',
      name: `${pre}feedback`,
      meta: {
        auth: ['admin-setting-store_service-feedback'],
        title: '用户留言'
      },
      component: () => import('@/pages/setting/storeService/feedback')
    },
  ]
}
