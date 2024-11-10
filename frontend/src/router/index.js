import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/layout/LayoutIndex.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/redirect',
      component: Layout,
      children: [
        {
          path: '/redirect/:path(.*)',
          component: () => import('@/views/redirect')
        }
      ]
    },
    {
      path: '/login',
      component: () => import('@/views/auth/UserLogin.vue'),
      hidden: true
    },
    {
      path: '/404',
      component: () => import('@/views/error-page/ErrorPage404.vue'),
      hidden: true
    },
    {
      path: '/',
      name: 'Root',
      component: Layout,
      redirect: '/task',
      children: [
        {
          path: 'task',
          name: 'Task',
          component: () => import('@/views/task/TaskIndex.vue')
        }
      ]
    },
    { path: '/:pathMatch(.*)', redirect: '/404' }
  ]
})

export default router
