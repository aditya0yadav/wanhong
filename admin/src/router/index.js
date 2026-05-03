import { createRouter, createWebHashHistory } from 'vue-router'
export const Layout = () => import('@/layout/index.vue')

/**
 * 静态路由
 */
export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    meta: { hidden: true },
    children: [
      {
        path: '/redirect/:path(.*)',
        component: () => import('@/views/system/components/SystemRedirect.vue')
      }
    ]
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/system/login.vue'),
    meta: { title: '登录', hidden: true }
  },

  {
    path: '/',
    name: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/system/index.vue'),
        meta: {
          title: 'dashboard',
          icon: 'home-filled',
          affix: true,
          keepAlive: true,
          alwaysShow: false
        }
      },
      {
        path: 'setting',
        component: () => import('@/views/system/components/SystemSetting.vue'),
        name: 'Setting',
        meta: { title: 'System setting', hidden: true }
      },
      {
        path: '401',
        name: '401',
        component: () => import('@/views/system/components/System401.vue'),
        meta: { title: '401', hidden: true }
      },
      {
        path: '404',
        name: '404',
        component: () => import('@/views/system/components/System404.vue'),
        meta: { title: '404', hidden: true }
      },
      {
        path: 'platform/detail/:id',
        name: 'PlatformDetail',
        component: () => import('@/views/platform/detail.vue'),
        meta: { title: '平台详情', hidden: true }
      },
      {
        path: 'team/team',
        name: 'TeamList',
        component: () => import('@/views/team/team.vue'),
        meta: { title: '全部团队', hidden: true }
      },
      {
        path: 'team/reward',
        name: 'TeamReward',
        component: () => import('@/views/team/reward.vue'),
        meta: { title: '业绩记录', hidden: true }
      },
      {
        path: 'team/member_list',
        name: 'TeamMemberList',
        component: () => import('@/views/team/member.vue'),
        meta: { title: '全部成员', hidden: true }
      },
      {
        path: 'team/persona',
        name: 'TeamPersona',
        component: () => import('@/views/team/persona.vue'),
        meta: { title: '业绩人设', hidden: true }
      },
      {
        path: 'team/log',
        name: 'TeamLog',
        component: () => import('@/views/team/log.vue'),
        meta: { title: '操作日志', hidden: true }
      },
      {
        path: 'file/file',
        name: 'AllFiles',
        component: () => import('@/views/file/file.vue'),
        meta: { title: '全部文件', hidden: true }
      },
      {
        path: 'file/export',
        name: 'ExportRecords',
        component: () => import('@/views/file/export.vue'),
        meta: { title: '导出记录', hidden: true }
      },
      {
        path: 'setting/notice',
        name: 'NoticeManagement',
        component: () => import('@/views/setting/notice.vue'),
        meta: { title: '通知管理', hidden: true }
      },
      {
        path: 'setting/setting',
        name: 'WebsiteConfig',
        component: () => import('@/views/setting/setting.vue'),
        meta: { title: '网站配置', hidden: true }
      }
    ]
  }
]

/**
 * 创建路由
 */
const router = createRouter({
  history: createWebHashHistory(),
  routes: constantRoutes,
  scrollBehavior: () => ({ left: 0, top: 0 })
})

/**
 * 重置路由
 */
export function resetRouter() {
  router.replace({ path: '/login' })
}

export default router
