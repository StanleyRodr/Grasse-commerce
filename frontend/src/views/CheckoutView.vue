<script setup lang="ts">
import { computed, ref } from 'vue'
import { ArrowLeft, Check, LockKeyhole, MapPin, ShoppingBag } from '@lucide/vue'
import { RouterLink, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { getAuthToken } from '../services/authService'
import { createAddress, createCheckoutSession, createOrder } from '../services/commerceService'

const router = useRouter()
const cart = useCartStore()
const submitted = ref(false)
const email = ref('')
const firstName = ref('')
const lastName = ref('')
const address = ref('')
const city = ref('')
const state = ref('')
const postalCode = ref('')
const errorMessage = ref('')
const orderNumber = ref('GR-2026-0018')
const money = (value: number) => `$${value.toLocaleString('es-MX')} MXN`
const subtotal = computed(() => cart.items.reduce((total, item) => total + item.price, 0))
const shipping = computed(() => subtotal.value >= 1500 ? 0 : 150)
const total = computed(() => subtotal.value + shipping.value)
const canSubmit = computed(() => Boolean(email.value && firstName.value && lastName.value && address.value && city.value && state.value && postalCode.value && cart.count))
const placeOrder = async () => {
  if (!canSubmit.value) return
  errorMessage.value = ''

  if (!getAuthToken()) {
    submitted.value = true
    return
  }

  try {
    const savedAddress = await createAddress({ label: 'Envío', recipient: `${firstName.value} ${lastName.value}`, line1: address.value, city: city.value, state: state.value, postal_code: postalCode.value, is_default: true })
    const order = await createOrder(savedAddress.data.id)
    orderNumber.value = `ORDEN #GR-${String(order.data.id).padStart(6, '0')}`
    const checkout = await createCheckoutSession(order.data.id)
    window.location.assign(checkout.data.url)
    submitted.value = true
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'No pudimos crear tu pedido.'
  }
}
</script>

<template>
  <div class="checkout-page">
    <header class="cart-page-header"><button class="back-link" @click="router.back()"><ArrowLeft :size="16" /> Volver al carrito</button><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><span class="detail-meta"><LockKeyhole :size="12" /> CHECKOUT SEGURO</span></header>
    <main v-if="!submitted" class="checkout-content"><div class="checkout-title"><p class="eyebrow">Último paso</p><h1>Completa tu <em>pedido</em></h1><p>Compra como invitado o conecta tu cuenta después.</p></div><div v-if="cart.count" class="checkout-layout"><form class="checkout-form" @submit.prevent="placeOrder"><section class="checkout-section"><div class="checkout-section-title"><span>01</span><div><h2>Datos de contacto</h2><p>Recibe confirmación de tu pedido.</p></div></div><label>Correo electrónico<input v-model="email" type="email" placeholder="tu@correo.com" required /></label></section><section class="checkout-section"><div class="checkout-section-title"><span>02</span><div><h2>Domicilio de envío</h2><p><MapPin :size="14" /> México</p></div></div><div class="form-grid"><label>Nombre<input v-model="firstName" type="text" placeholder="Tu nombre" required /></label><label>Apellidos<input v-model="lastName" type="text" placeholder="Tus apellidos" required /></label><label class="full-field">Domicilio<input v-model="address" type="text" placeholder="Calle y número" required /></label><label>Ciudad<input v-model="city" type="text" placeholder="Ciudad" required /></label><label>Estado<input v-model="state" type="text" placeholder="Estado" required /></label><label>Código postal<input v-model="postalCode" type="text" inputmode="numeric" placeholder="00000" required /></label></div></section><button class="primary-button place-order" type="submit" :disabled="!canSubmit">Continuar a pago <ArrowLeft :size="16" /></button></form><aside class="checkout-summary"><p class="eyebrow">Tu pedido</p><div v-for="(item, index) in cart.items" :key="`${item.id}-${index}`" class="checkout-item"><img :src="item.image" :alt="item.name" /><div><strong>{{ item.name }}</strong><span>{{ item.house }}</span></div><b>{{ money(item.price) }}</b></div><div class="summary-lines"><div><span>Subtotal</span><strong>{{ money(subtotal) }}</strong></div><div><span>Envío</span><strong>{{ shipping ? money(shipping) : 'Gratis' }}</strong></div><div class="summary-total"><span>Total</span><strong>{{ money(total) }}</strong></div></div><p class="secure-note"><ShoppingBag :size="13" /> Stripe en modo prueba. No se realizará ningún cargo real.</p></aside></div><div v-else class="checkout-empty"><ShoppingBag :size="34" /><h2>Carrito vacío</h2><RouterLink to="/catalogo" class="text-link">Volver a la colección <ArrowLeft :size="15" /></RouterLink></div></main>
     <main v-else class="order-success"><div class="success-icon"><Check :size="30" /></div><p class="eyebrow">Pedido recibido</p><h1>Gracias por elegir <em>Grasse</em>.</h1><p>Tu pedido fue registrado correctamente. Enviaremos confirmación a <strong>{{ email }}</strong>.</p><span class="order-number">{{ orderNumber }}</span><RouterLink to="/" class="primary-button">Volver al inicio <ArrowLeft :size="16" /></RouterLink></main>
     <p v-if="errorMessage" class="auth-error" role="alert">{{ errorMessage }}</p>
  </div>
</template>
