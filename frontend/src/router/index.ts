import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProductView from '../views/ProductView.vue'
import CartView from '../views/CartView.vue'
import CheckoutView from '../views/CheckoutView.vue'
import AuthView from '../views/AuthView.vue'
import AccountView from '../views/AccountView.vue'
import AdminView from '../views/AdminView.vue'
import VerifyEmailView from '../views/VerifyEmailView.vue'
import { getAuthToken, getAuthUser, getCurrentUser } from '../services/authService'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/catalogo', name: 'catalog', component: HomeView },
    { path: '/producto/:id', name: 'product', component: ProductView },
    { path: '/carrito', name: 'cart', component: CartView },
    { path: '/checkout', name: 'checkout', component: CheckoutView },
    { path: '/login', name: 'login', component: AuthView },
    { path: '/registro', name: 'register', component: AuthView },
    { path: '/recuperar-contrasena', name: 'forgot-password', component: AuthView },
    { path: '/restablecer-contrasena', name: 'reset-password', component: AuthView },
    { path: '/verificar-correo', name: 'verify-email', component: VerifyEmailView, meta: { requiresAuth: true } },
    { path: '/cuenta', name: 'account', component: AccountView },
    { path: '/admin', name: 'admin', component: AdminView, meta: { requiresAuth: true, requiresAdmin: true } },
  ],
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
  if (to.meta.requiresAuth && !getAuthToken()) return { name: 'login' }
  if (to.meta.requiresAdmin) {
    const user = getAuthUser() ?? await getCurrentUser()
    if (user?.role !== 'admin') return { name: 'home' }
  }
})
