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

const pre = 'app_'

export default {
  path: '/admin/app',
  name: 'app',
  header: 'app',
  redirect: {
    name: `${pre}wechatMenus`
  },
  meta: {
    auth: ['admin-app']
  },
  component: BasicLayout,
  children: [
    {
      path: 'wechat/wechat_user/user/tag',
      name: `${pre}tag`,
      meta: {
        auth: ['wechat-wechat-user-tag'],
        title: '用户标签'
      },
      component: () => import('@/pages/app/wechat/user/tag')
    },
    {
      path: 'wechat/wechat_user/user/group',
      name: `${pre}group`,
      meta: {
        auth: ['wechat-wechat-user-group'],
        title: '用户分组'
      },
      component: () => import('@/pages/app/wechat/user/tag')
    },
  ]
}
