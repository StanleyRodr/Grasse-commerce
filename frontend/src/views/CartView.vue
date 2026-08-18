<script setup lang="ts">
import { computed } from 'vue'
import { ArrowLeft, Minus, Plus, ShoppingBag, Trash2 } from '@lucide/vue'
import { RouterLink, useRouter } from 'vue-router'
import { useCartStore, type CartProduct } from '../stores/cart'

const router = useRouter()
const cart = useCartStore()
const groupedItems = computed(() => {
  const groups = new Map<string, { product: CartProduct; quantity: number }>()
  cart.items.forEach((product) => {
    const key = `${product.id}-${product.name}`
    const current = groups.get(key)
    if (current) current.quantity += 1
    else groups.set(key, { product, quantity: 1 })
  })
  return [...groups.values()]
})
const subtotal = computed(() => cart.items.reduce((total, item) => total + item.price, 0))
const shipping = computed(() => subtotal.value >= 1500 || subtotal.value === 0 ? 0 : 150)
const total = computed(() => subtotal.value + shipping.value)
const money = (value: number) => `$${value.toLocaleString('es-MX')} MXN`
const addOne = (product: CartProduct) => cart.add(product)
</script>

<template>
  <div class="cart-page">
    <header class="cart-page-header"><button class="back-link" @click="router.back()"><ArrowLeft :size="16" /> Volver</button><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><span class="detail-meta">CARRITO / {{ cart.count }} PRODUCTOS</span></header>
    <main class="cart-content"><div class="cart-title"><p class="eyebrow">Tu selección</p><h1>Carrito <em>de compras</em></h1><p>{{ cart.count }} {{ cart.count === 1 ? 'producto' : 'productos' }} seleccionados</p></div><div v-if="groupedItems.length" class="cart-layout"><section class="cart-items"><article v-for="item in groupedItems" :key="`${item.product.id}-${item.product.name}`" class="cart-item"><img :src="item.product.image" :alt="item.product.name" /><div class="cart-item-info"><p class="product-house">{{ item.product.house }}</p><h2>{{ item.product.name }}</h2><p class="cart-item-price">{{ money(item.product.price) }}</p><div class="cart-item-actions"><div class="quantity-control"><button aria-label="Reducir cantidad" :disabled="item.quantity === 1" @click="cart.removeOne(item.product)"><Minus :size="14" /></button><span>{{ item.quantity }}</span><button aria-label="Aumentar cantidad" @click="addOne(item.product)"><Plus :size="14" /></button></div><button class="remove-button" @click="cart.removeAll(item.product)"><Trash2 :size="14" /> Eliminar</button></div></div><p class="cart-item-total">{{ money(item.product.price * item.quantity) }}</p></article><RouterLink to="/catalogo" class="continue-shopping"><ArrowLeft :size="15" /> Seguir comprando</RouterLink></section><aside class="summary-card"><p class="eyebrow">Resumen</p><div><span>Subtotal</span><strong>{{ money(subtotal) }}</strong></div><div><span>Envío</span><strong>{{ shipping ? money(shipping) : 'Gratis' }}</strong></div><div class="summary-total"><span>Total</span><strong>{{ money(total) }}</strong></div><RouterLink to="/checkout" class="primary-button checkout-button">Continuar al checkout <ArrowLeft :size="16" /></RouterLink><p class="secure-note">Pago seguro · Stripe en modo prueba</p></aside></div><div v-else class="empty-cart"><ShoppingBag :size="36" /><h2>Tu carrito está vacío</h2><p>Descubre una fragancia que cuente tu historia.</p><RouterLink to="/catalogo" class="primary-button">Explorar colección <ArrowLeft :size="16" /></RouterLink></div></main>
  </div>
</template>
