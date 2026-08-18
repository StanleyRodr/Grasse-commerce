import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProductView from '../views/ProductView.vue'
import CartView from '../views/CartView.vue'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/catalogo', name: 'catalog', component: HomeView },
    { path: '/producto/:id', name: 'product', component: ProductView },
    { path: '/carrito', name: 'cart', component: CartView },
  ],
  scrollBehavior: () => ({ top: 0 }),
})
