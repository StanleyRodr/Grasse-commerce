<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { ArrowLeft, Check, ChevronRight, Heart, MapPin, Package, Pencil, Star } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import { logout } from '../services/authService'
import { getAddresses, getOrders } from '../services/commerceService'
import { useRouter } from 'vue-router'

const activeSection = ref('Resumen')
const sections = ['Resumen', 'Perfil', 'Domicilios', 'Pedidos', 'Wishlist', 'Reseñas']
const saved = ref(false)
const addresses = ref<Awaited<ReturnType<typeof getAddresses>>['data']>([])
const orders = ref([
  { id: 'GR-2026-0012', date: '18 mayo 2026', status: 'Enviado', total: '$4,250 MXN', items: 'Santal 33 · 50 ml' },
  { id: 'GR-2026-0007', date: '02 abril 2026', status: 'Entregado', total: '$3,120 MXN', items: 'Gris Charnel · 50 ml' },
])
const wishlist = [
  { name: 'Another 13', house: 'Le Labo', price: '$3,980 MXN', image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=500&q=85' },
  { name: 'Bal d’Afrique', house: 'Byredo', price: '$4,890 MXN', image: 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?auto=format&fit=crop&w=500&q=85' },
]
const router = useRouter()
const handleLogout = async () => {
  await logout()
  await router.push({ name: 'login' })
}
let logoutButton: HTMLButtonElement | null = null
onMounted(() => {
  logoutButton = document.querySelector<HTMLButtonElement>('.logout-button')
  logoutButton?.addEventListener('click', handleLogout)
  void getOrders().then((response) => {
    const statusLabels: Record<string, string> = { pending: 'Pendiente', processing: 'En proceso', shipped: 'Enviado', delivered: 'Entregado', cancelled: 'Cancelado' }
    orders.value = response.data.data.map((order) => ({ id: `GR-${String(order.id).padStart(6, '0')}`, date: new Date(order.created_at).toLocaleDateString('es-MX'), status: statusLabels[order.status] ?? order.status, total: `$${order.total.toLocaleString('es-MX')} MXN`, items: order.items.map((item) => `${item.product_name} · ${item.quantity}`).join(', ') }))
  }).catch(() => undefined)
  void getAddresses().then((response) => {
    addresses.value = response.data
    saved.value = response.data.some((address) => address.is_default)
  }).catch(() => undefined)
})
onUnmounted(() => logoutButton?.removeEventListener('click', handleLogout))
</script>

<template>
  <div class="account-page"><header class="account-header"><RouterLink to="/" class="back-link"><ArrowLeft :size="16" /> Volver a Grasse</RouterLink><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><RouterLink to="/carrito" class="account-cart">Carrito <ChevronRight :size="14" /></RouterLink></header><main class="account-layout"><aside class="account-sidebar"><div class="account-avatar">LM</div><p class="account-greeting">Hola, <strong>Lucía</strong></p><p class="account-email">lucia@correo.com</p><nav><button v-for="section in sections" :key="section" :class="{ active: activeSection === section }" @click="activeSection = section">{{ section }} <ChevronRight :size="14" /></button></nav><button class="logout-button">Cerrar sesión</button></aside><section class="account-main"><div class="account-main-heading"><div><p class="eyebrow">Mi espacio</p><h1>{{ activeSection }}</h1></div><span class="account-date">MAYO 2026</span></div><template v-if="activeSection === 'Resumen'"><div class="account-stats"><div><Package :size="18" /><strong>2</strong><span>Pedidos realizados</span></div><div><Star :size="18" /><strong>14</strong><span>Reseñas publicadas</span></div><div><Heart :size="18" /><strong>2</strong><span>Favoritos guardados</span></div></div><div class="account-block"><div class="block-heading"><div><p class="eyebrow">Actividad reciente</p><h2>Últimos pedidos</h2></div><button @click="activeSection = 'Pedidos'">Ver todos <ChevronRight :size="14" /></button></div><div class="order-row" v-for="order in orders" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>{{ order.id }}</strong><span>{{ order.items }} · {{ order.date }}</span></div><span class="order-status" :class="order.status.toLowerCase()">{{ order.status }}</span><b>{{ order.total }}</b></div></div><div class="account-block"><div class="block-heading"><div><p class="eyebrow">Tu selección</p><h2>Wishlist</h2></div><button @click="activeSection = 'Wishlist'">Ver todos <ChevronRight :size="14" /></button></div><div class="wishlist-mini"><div v-for="item in wishlist" :key="item.name"><img :src="item.image" :alt="item.name" /><p>{{ item.house }}</p><h3>{{ item.name }}</h3><span>{{ item.price }}</span></div></div></div></template><template v-else-if="activeSection === 'Perfil'"><div class="account-form-card"><div class="block-heading"><div><p class="eyebrow">Información personal</p><h2>Tus datos</h2></div><Pencil :size="17" /></div><div class="profile-fields"><label>Nombre completo<input value="Lucía Martínez" /></label><label>Correo electrónico<input value="lucia@correo.com" type="email" /></label><label>Teléfono<input value="+52 55 1234 5678" /></label><label>Fecha de nacimiento<input value="12 / 08 / 1997" /></label></div><button class="primary-button save-profile" @click="saved = true">{{ saved ? 'Cambios guardados' : 'Guardar cambios' }} <Check v-if="saved" :size="16" /></button></div></template><template v-else-if="activeSection === 'Domicilios'"><div class="address-grid"><article class="address-card default"><span class="default-label">Predeterminado</span><MapPin :size="19" /><strong>Casa</strong><p>Lucía Martínez<br />Av. Reforma 245, Int. 4<br />Cuauhtémoc, CDMX<br />06600</p><button>Editar</button></article><article class="address-card add-address"><MapPin :size="21" /><strong>Agregar domicilio</strong><p>Guarda otra dirección para agilizar tus próximas compras.</p><button>+ Añadir nuevo</button></article></div></template><template v-else-if="activeSection === 'Pedidos'"><div class="account-block orders-block"><div class="order-row" v-for="order in orders" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>{{ order.id }}</strong><span>{{ order.items }} · {{ order.date }}</span></div><span class="order-status" :class="order.status.toLowerCase()">{{ order.status }}</span><b>{{ order.total }}</b><ChevronRight :size="15" /></div></div></template><template v-else-if="activeSection === 'Wishlist'"><div class="wishlist-large"><RouterLink v-for="item in wishlist" :key="item.name" to="/producto/2"><img :src="item.image" :alt="item.name" /><p>{{ item.house }}</p><h2>{{ item.name }}</h2><span>{{ item.price }}</span></RouterLink></div></template><template v-else><div class="account-block reviews-account"><div class="review-account-row"><div class="review-stars"><Star v-for="star in 5" :key="star" :size="14" fill="currentColor" /></div><strong>Santal 33</strong><span>“Mi perfume diario, elegante y cálido.”</span><small>Publicado 12 mayo 2026</small></div><div class="review-account-row"><div class="review-stars"><Star v-for="star in 4" :key="star" :size="14" fill="currentColor" /></div><strong>Gris Charnel</strong><span>“Una estela suave y muy especial.”</span><small>Publicado 04 abril 2026</small></div></div></template></section></main></div>
</template>
