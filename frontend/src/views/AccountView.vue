<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { ArrowLeft, Check, ChevronRight, Heart, MapPin, Package, Plus, Trash2 } from '@lucide/vue'
import { RouterLink, useRouter } from 'vue-router'
import { clearAuthToken, getAuthUser, logout } from '../services/authService'
import { createAddress, deleteAddress, getAddresses, getOrders, setDefaultAddress, type Address, type AddressInput } from '../services/commerceService'
import ProductCard from '../components/ProductCard.vue'
import { getWishlist, removeFromWishlist } from '../services/wishlistService'
import { useCartStore } from '../stores/cart'
import type { Product } from '../types/catalog'

const router = useRouter()
const cart = useCartStore()
const user = getAuthUser()
const activeSection = ref('Resumen')
const sections = ['Resumen', 'Perfil', 'Domicilios', 'Pedidos', 'Wishlist', 'Reseñas']
const addresses = ref<Address[]>([])
const orders = ref<Array<{ id: string; date: string; status: string; total: string; items: string }>>([])
const wishlist = ref<Product[]>([])
const showAddressForm = ref(false)
const savingAddress = ref(false)
const addressError = ref('')
const addressForm = ref<AddressInput>({ label: 'Casa', recipient: user?.name ?? '', line1: '', city: '', state: '', postal_code: '', phone: '', is_default: false })

const loadAddresses = async () => {
  try { addresses.value = (await getAddresses()).data } catch { addresses.value = [] }
}
const loadOrders = async () => {
  try {
    const response = await getOrders()
    const labels: Record<string, string> = { pending: 'Pendiente', processing: 'En proceso', shipped: 'Enviado', delivered: 'Entregado', cancelled: 'Cancelado' }
    orders.value = response.data.data.map((order) => ({ id: `GR-${String(order.id).padStart(6, '0')}`, date: new Date(order.created_at).toLocaleDateString('es-MX'), status: labels[order.status] ?? order.status, total: `$${order.total.toLocaleString('es-MX')} MXN`, items: order.items.map((item) => `${item.product_name} · ${item.quantity}`).join(', ') }))
  } catch { orders.value = [] }
}
const loadWishlist = async () => {
  try {
    wishlist.value = (await getWishlist()).data.map(({ product }) => ({ ...product, category: '', scentFamily: '', occasion: '', rating: 0, reviews: 0 }))
  } catch { wishlist.value = [] }
}
const removeWishlistItem = async (id: number) => {
  await removeFromWishlist(id)
  wishlist.value = wishlist.value.filter((product) => product.id !== id)
}
const saveAddress = async () => {
  addressError.value = ''
  savingAddress.value = true
  try {
    await createAddress(addressForm.value)
    addressForm.value = { label: 'Casa', recipient: user?.name ?? '', line1: '', city: '', state: '', postal_code: '', phone: '', is_default: false }
    showAddressForm.value = false
    await loadAddresses()
  } catch (error) { addressError.value = error instanceof Error ? error.message : 'No pudimos guardar el domicilio.' }
  finally { savingAddress.value = false }
}
const makeDefault = async (id: number) => { await setDefaultAddress(id); await loadAddresses() }
const removeAddress = async (id: number) => { await deleteAddress(id); await loadAddresses() }
const handleLogout = async () => { await logout(); clearAuthToken(); await router.push({ name: 'login' }) }
onMounted(() => { void loadAddresses(); void loadOrders(); void loadWishlist() })
</script>

<template>
  <div class="account-page">
    <header class="account-header"><RouterLink to="/" class="back-link"><ArrowLeft :size="16" /> Volver a Grasse</RouterLink><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><RouterLink to="/carrito" class="account-cart">Carrito <ChevronRight :size="14" /></RouterLink></header>
    <main class="account-layout">
      <aside class="account-sidebar"><div class="account-avatar">{{ user?.name?.slice(0, 2).toUpperCase() ?? 'GR' }}</div><p class="account-greeting">Hola, <strong>{{ user?.name ?? 'Cliente' }}</strong></p><p class="account-email">{{ user?.email ?? '' }}</p><nav><button v-for="section in sections" :key="section" :class="{ active: activeSection === section }" @click="activeSection = section">{{ section }} <ChevronRight :size="14" /></button></nav><button class="logout-button" @click="handleLogout">Cerrar sesión</button></aside>
      <section class="account-main"><div class="account-main-heading"><div><p class="eyebrow">Mi espacio</p><h1>{{ activeSection }}</h1></div><span class="account-date">GRASSE / CUENTA</span></div>
        <template v-if="activeSection === 'Resumen'"><div class="account-stats"><div><Package :size="18" /><strong>{{ orders.length }}</strong><span>Pedidos realizados</span></div><div><MapPin :size="18" /><strong>{{ addresses.length }}</strong><span>Domicilios guardados</span></div><div><Heart :size="18" /><strong>--</strong><span>Favoritos guardados</span></div></div><div class="account-block"><div class="block-heading"><div><p class="eyebrow">Actividad reciente</p><h2>Últimos pedidos</h2></div><button @click="activeSection = 'Pedidos'">Ver todos <ChevronRight :size="14" /></button></div><p v-if="!orders.length">Aún no tienes pedidos registrados.</p><div class="order-row" v-for="order in orders.slice(0, 3)" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>{{ order.id }}</strong><span>{{ order.items }} · {{ order.date }}</span></div><span class="order-status">{{ order.status }}</span><b>{{ order.total }}</b></div></div></template>
        <template v-else-if="activeSection === 'Domicilios'"><div class="account-block"><div class="block-heading"><div><p class="eyebrow">Envíos</p><h2>Tus domicilios</h2></div><button class="primary-button" @click="showAddressForm = !showAddressForm"><Plus :size="15" /> Nuevo domicilio</button></div><form v-if="showAddressForm" class="checkout-form" @submit.prevent="saveAddress"><div class="form-grid"><label>Etiqueta<input v-model="addressForm.label" required /></label><label>Destinatario<input v-model="addressForm.recipient" required /></label><label class="full-field">Domicilio<input v-model="addressForm.line1" required /></label><label>Ciudad<input v-model="addressForm.city" required /></label><label>Estado<input v-model="addressForm.state" required /></label><label>Código postal<input v-model="addressForm.postal_code" pattern="[0-9]{5}" required /></label><label>Teléfono<input v-model="addressForm.phone" /></label></div><p v-if="addressError" class="auth-error">{{ addressError }}</p><button class="primary-button" type="submit" :disabled="savingAddress">{{ savingAddress ? 'Guardando...' : 'Guardar domicilio' }}</button></form><p v-if="!addresses.length && !showAddressForm">No tienes domicilios guardados.</p><article v-for="address in addresses" :key="address.id" class="address-card"><div><p class="eyebrow">{{ address.label }} <span v-if="address.is_default">· Predeterminado</span></p><strong>{{ address.recipient }}</strong><p>{{ address.line1 }}, {{ address.city }}, {{ address.state }} · {{ address.postal_code }}</p></div><div><button v-if="!address.is_default" class="text-link" @click="makeDefault(address.id)"><Check :size="14" /> Usar por defecto</button><button class="remove-button" @click="removeAddress(address.id)"><Trash2 :size="14" /> Eliminar</button></div></article></div></template>
        <template v-else-if="activeSection === 'Pedidos'"><div class="account-block"><p v-if="!orders.length">Aún no tienes pedidos registrados.</p><div class="order-row" v-for="order in orders" :key="order.id"><div class="order-icon"><Package :size="17" /></div><div class="order-info"><strong>{{ order.id }}</strong><span>{{ order.items }} · {{ order.date }}</span></div><span class="order-status">{{ order.status }}</span><b>{{ order.total }}</b></div></div></template>
         <template v-else-if="activeSection === 'Wishlist'"><div class="account-block"><p v-if="!wishlist.length">Aún no tienes favoritos guardados.</p><div v-else class="wishlist-account-grid"><ProductCard v-for="product in wishlist" :key="product.id" :product="product" :liked="true" :adding="false" @toggle-like="removeWishlistItem" @add="cart.add" /></div></div></template>
         <template v-else><div class="account-block"><p class="eyebrow">Próximamente</p><h2>{{ activeSection }}</h2><p>Esta sección conserva su diseño inicial mientras se conecta al siguiente servicio de la API.</p></div></template>
      </section>
    </main>
  </div>
</template>
