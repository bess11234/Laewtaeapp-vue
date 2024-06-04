import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '@/views/HomeView.vue'
import NotFound from '@/views/NotFound.vue'
import ErrorPage from '@/views/ErrorView.vue'
import ProfileView from '@/views/ProfileView.vue'
import OrderFoods from '@/views/OrderView.vue'
import MCoupons from '@/components/member/MCoupons.vue'
import MGachabox from '@/components/member/MGachabox.vue'
import MyCoupon from '@/components/member/MyCoupon.vue'

const isCredential = localStorage.getItem('token') != ''

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },

    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },

    {
      path: '/orderFoods',
      name: 'orderFoods',
      component: OrderFoods
    },

    {
      path: '/coupons',
      name: 'coupons',
      component: MCoupons
    },

    {
      path: '/manage',
      name: 'manage',
      component: '',
      meta: { requiresAuth: true },
      children: [
        {
          path: 'tables',
          name: 'manageTables',
          component: () => import('../components/manager/ManageTables.vue')
        },
        {
          path: 'menus',
          name: 'manageMenus',
          component: () => import('../components/manager/ManageMenus.vue')
        },
        {
          path: 'members',
          name: 'manageMembers',
          component: () => import('../components/manager/ManageMembers.vue')
        },
        {
          path: 'gacha',
          name: 'manageGacha',
          component: () => import('../components/manager/ManageGacha.vue')
        },
        {
          path: 'bills',
          name: 'manageBills',
          component: () => import('../components/manager/ManageBills.vue')
        },
        {
          path: 'coupons',
          name: 'manageCoupons',
          component: () => import('../components/manager/ManageCoupons.vue')
        }
      ]
    },

    {
      path: '/staff',
      name: 'staff',
      component: '',
      meta: { requiresAuth: true },
      children: [
        {
          path: 'tables',
          name: 'staffTables',
          component: () => import('../components/staff/StaffTables.vue')
        },
        {
          path: 'orders',
          name: 'staffOrders',
          component: () => import('../components/staff/StaffOrders.vue'),
          children: [
            {
              path: 'waiter',
              name: 'orderWaiter',
              component: () => import('../components/staff/WaiterOrders.vue')
            },
            {
              path: 'chef',
              name: 'orderChef',
              component: () => import('../components/staff/ChefOrders.vue')
            },
          ]
        },
      ]
    },

    {
      path: '/myCoupon',
      name: 'myCoupon',
      meta: { requiresAuth: true },
      component: MyCoupon
    },

    {
      path: '/gachabox',
      name: 'gachabox',
      meta: { requiresAuth: true },
      component: MGachabox
    },

    {
      path: '/profile',
      name: 'profile',
      meta: { requiresAuth: true },
      component: ProfileView
    },

    {
      path: '/Error/:error',
      name: 'errorPage',
      component: ErrorPage,
      props: true
    },

    {
      path: '/:catchAll(.*)',
      name: 'notFound',
      component: NotFound
    }

  ]
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth == true) {
    if (isCredential) {
      next()
    } else {
      router.go(-1)
    }
  } else {
    next()
  }
})

export default router
