import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/layout/layout.vue'
import NProgress from "@/config/nprogress";
const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      component: Layout,
      redirect: '/offers',
      children: [{
        path: 'offers',
        name: 'offers',
        meta: {
          title: `offers`,
        },
        component: () => import('../views/offers.vue')
      },{
        path: 'platform/:id',
        name: 'platform',
        meta: {
          title: `platform`,
          activeMenu: 'offers',
          checkLogin: true
        },
        component: () => import('../views/platOffers.vue')
      },{
        path: 'notice/:id',
        name: 'notice',
        meta: {
          title: `notice`,
          checkLogin: true,
        },
        component: () => import('../views/notice.vue')
      },{
        path: '/my/statistics',
        name: 'statistics',
        meta: {
          title: `statistics`
        },
        component: () => import('../views/statistics.vue')
      },{
        path: '/team/statistics',
        name: 'teamStatistics',
        component: () => import('../views/team_statistics.vue')
      },{
        path: 'users',
        name: 'users',
        meta: {
          title: `User Management`,
          checkLogin: true
        },
        component: () => import('../views/user.vue')
      },{
        path: 'ranking',
        name: 'ranking',
        meta: {
          title: `Ranking`
        },
        component: () => import('../views/ranking.vue')
      },{
        path: 'leaderboard',
        name: 'leaderboard',
        meta: {
          title: `leaderboard`,
        },
        component: () => import('../views/leaderboard.vue')
      },{
        path: 'detail/daily',
        name: 'daily',
        meta: {
          title: `daily-leaderboard`,
          activeMenu: 'leaderboard'
        },
        component: () => import('../views/daily-leaderboard.vue')
      },{
        path: 'detail/weekly',
        name: 'weekly',
        meta: {
          title: `weekly-leaderboard`,
          activeMenu: 'leaderboard'
        },
        component: () => import('../views/weekly-leaderboard.vue')
      },{
        path: 'detail/monthly',
        name: 'monthly',
        meta: {
          title: `monthly-leaderboard`,
          activeMenu: 'leaderboard'
        },
        component: () => import('../views/monthly-leaderboard.vue')
      },{
        path: 'profile',
        name: 'profile',
        meta: {
          title: `profile`,
          checkLogin: true,
        },
        component: () => import('../views/profile.vue')
      }]
    },{
      path: '/unauth',
      name: 'unauth',
      component: () => import('../views/unauth.vue')
    },{
      path: '/login',
      name: 'login',
      component: () => import('../views/login.vue')
    },{
      path: '/detail',
      name: 'surveyDetail',
      component: () => import('../views/surveyDetail.vue')
    }
  ]
})
router.beforeEach(async (to, from, next) => {
  // 1.NProgress 开始
  NProgress.start();

  if(to.meta && to.meta.title){
    document.title = `${to.meta.title}`
  }
  next();
});
/**
 * @description 路由跳转结束
 * */
router.afterEach(() => {
  NProgress.done();
});
export default router
