import { createRouter, createWebHistory } from 'vue-router'
import Products from '@/components/Products/Index.vue'
import Home from '@/components/Home.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/',
      name: 'products',
      component: Products
    }
  ],
})

export default router
