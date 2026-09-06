<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { ArrowLeft, Check, ChevronDown, Heart, Minus, Plus, ShoppingBag, Star } from '@lucide/vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { getProductById, getProducts } from '../services/catalogService'
import type { Product } from '../types/catalog'
import { createReview, getReviews } from '../services/reviewService'

const route = useRoute()
const router = useRouter()
const cart = useCartStore()
const liked = ref(false)
const added = ref(false)
const quantity = ref(1)
const selectedSize = ref('50 ml')
const showReviewForm = ref(false)
const reviewSubmitted = ref(false)
const reviewRating = ref(0)
const reviewText = ref('')

const product = ref<Product>({
  id: Number(route.params.id), name: 'Cargando...', house: '', category: '', scentFamily: '', occasion: '',
  price: 0, rating: 0, reviews: 0, reviewCount: 0, image: '', description: '', notes: [],
})
const loadError = ref('')

const sizes = computed(() => [
  ...(product.value.variants?.length ? product.value.variants.map((variant) => ({ label: variant.label, price: variant.price })) : [
    { label: '30 ml', price: Math.max(0, product.value.price - 1470) },
    { label: '50 ml', price: product.value.price },
    { label: '100 ml', price: product.value.price + 1450 },
  ]),
])
const currentPrice = computed(() => sizes.value.find((size) => size.label === selectedSize.value)?.price ?? product.value.price)
const reviews = ref<Array<{ name: string; date: string; rating: number; text: string; verified: boolean }>>([])
const relatedProducts = ref<Product[]>([])

onMounted(async () => {
  try {
    product.value = (await getProductById(Number(route.params.id))).data
    relatedProducts.value = (await getProducts({ per_page: 6 })).data.filter((item) => item.id !== product.value.id).slice(0, 2)
    const response = await getReviews(product.value.id)
    reviews.value = response.data.data.map((review) => ({ name: review.user.name, date: new Date(review.created_at).toLocaleDateString('es-MX'), rating: review.rating, text: review.comment, verified: review.verified_purchase }))
  } catch {
    loadError.value = 'No pudimos cargar este producto.'
  }
})

const addToCart = () => {
  const variant = product.value.variants?.find((item) => item.label === selectedSize.value)
  for (let index = 0; index < quantity.value; index += 1) cart.add({ id: product.value.id, variantId: variant?.id, variantLabel: selectedSize.value, name: `${product.value.name} · ${selectedSize.value}`, house: product.value.house, price: currentPrice.value, image: product.value.image })
  added.value = true
  window.setTimeout(() => { added.value = false }, 1800)
}
const submitReview = async () => {
  if (!reviewRating.value || !reviewText.value.trim()) return
  try {
    await createReview(product.value.id, reviewRating.value, reviewText.value.trim())
    reviewSubmitted.value = true
    showReviewForm.value = false
  } catch {
    reviewSubmitted.value = false
  }
}
const starState = (star: number, rating: number) => {
  if (rating >= star) return 'is-filled'
  if (rating >= star - 0.5) return 'is-partial'
  return ''
}
</script>

<template>
  <div class="product-page">
    <header class="product-page-header"><button class="back-link" @click="router.back()"><ArrowLeft :size="16" /> Volver a la colección</button><RouterLink class="wordmark" to="/">grasse<span>.</span></RouterLink><RouterLink to="/carrito" class="detail-meta">CARRITO ({{ cart.count }})</RouterLink></header>
    <main>
      <section class="product-detail"><div class="detail-image"><img :src="product.image" :alt="product.name" /><span class="detail-image-label">GRASSE / {{ product.category.toUpperCase() }}</span></div><div class="detail-copy"><div class="detail-breadcrumb">Inicio <span>/</span> Perfumes <span>/</span> {{ product.name }}</div><p class="eyebrow">{{ product.house }}</p><h1>{{ product.name }}</h1><div class="detail-rating"><span class="star-row" :aria-label="`${product.rating} de 5 estrellas`"><span v-for="star in 5" :key="star" class="rating-star" :class="starState(star, product.rating)">★</span></span> <strong>{{ product.rating }}</strong> <span>({{ product.reviewCount }} reseñas)</span></div><p class="detail-description">{{ product.description }}</p><div class="notes-row"><div><p class="field-label">NOTAS PRINCIPALES</p><div class="notes-list"><span v-for="note in product.notes" :key="note">{{ note }}</span></div></div></div><div class="detail-divider"></div><div class="size-row"><div><p class="field-label">PRESENTACIÓN</p><div class="size-options"><button v-for="size in sizes" :key="size.label" :class="{ selected: selectedSize === size.label }" @click="selectedSize = size.label">{{ size.label }}</button></div></div><p class="detail-price">${{ currentPrice.toLocaleString('es-MX') }} <small>MXN</small></p></div><div class="purchase-row"><div class="quantity-control"><button aria-label="Reducir cantidad" :disabled="quantity === 1" @click="quantity -= 1"><Minus :size="14" /></button><span>{{ quantity }}</span><button aria-label="Aumentar cantidad" @click="quantity += 1"><Plus :size="14" /></button></div><button class="primary-button detail-add-button" :class="{ 'is-added': added }" @click="addToCart">{{ added ? 'Agregado al carrito' : 'Agregar al carrito' }} <Check v-if="added" :size="17" /><ShoppingBag v-else :size="17" /></button><button class="detail-heart" :class="{ liked }" aria-label="Agregar a favoritos" @click="liked = !liked"><Heart :size="20" :fill="liked ? 'currentColor' : 'none'" /></button></div><div class="detail-perks"><span><Check :size="14" /> En stock · 18 disponibles</span><span><Check :size="14" /> Envío gratis desde $1,500</span><span><Check :size="14" /> Devoluciones en 30 días</span></div></div></section>

      <section class="reviews-section"><div class="reviews-heading"><div><p class="eyebrow">La comunidad Grasse</p><h2>Lo que dicen de <em>{{ product.name }}</em></h2></div><button class="review-cta" @click="showReviewForm = !showReviewForm">{{ showReviewForm ? 'Cerrar formulario' : 'Escribir una reseña' }} <ArrowLeft v-if="showReviewForm" :size="15" /><Plus v-else :size="15" /></button></div><div class="reviews-layout"><div class="rating-summary"><div class="rating-number">{{ product.rating }} <Star :size="22" fill="currentColor" /></div><p>Basado en {{ product.reviewCount }} reseñas</p><div v-for="(percentage, index) in [88, 7, 3, 1, 1]" :key="index" class="rating-bar"><span>{{ 5 - index }}</span><Star :size="11" fill="currentColor" /><div><i :style="{ width: `${percentage}%` }"></i></div><small>{{ percentage }}%</small></div></div><div class="review-list"><div v-if="showReviewForm" class="review-form"><div v-if="reviewSubmitted" class="review-success"><Check :size="16" /> Tu reseña se ha guardado para revisión.</div><template v-else><p class="field-label">TU EXPERIENCIA</p><div class="input-stars"><button v-for="star in 5" :key="star" :aria-label="`${star} estrellas`" @click="reviewRating = star"><Star :size="20" :fill="star <= reviewRating ? 'currentColor' : 'none'" /></button></div><textarea v-model="reviewText" placeholder="Cuéntanos qué te pareció este perfume..."></textarea><button class="primary-button" :disabled="!reviewRating || !reviewText.trim()" @click="submitReview">Publicar reseña <ArrowRight :size="16" /></button></template></div><article v-for="review in reviews" :key="review.name" class="review-item"><div class="review-top"><div><strong>{{ review.name }}</strong><span v-if="review.verified" class="verified"><Check :size="11" /> Compra verificada</span></div><time>{{ review.date }}</time></div><div class="review-stars"><Star v-for="star in 5" :key="star" :size="13" :fill="star <= review.rating ? 'currentColor' : 'none'" /></div><p>{{ review.text }}</p></article><button class="load-reviews">Cargar más reseñas <ChevronDown :size="15" /></button></div></div></section>

       <section class="related-section"><div class="section-heading"><div><p class="eyebrow">También te puede gustar</p><h2>Descubre algo nuevo</h2></div><RouterLink class="text-link" to="/catalogo">Ver colección <ArrowLeft :size="15" /></RouterLink></div><div class="related-grid"><RouterLink v-for="item in relatedProducts" :key="item.id" :to="`/producto/${item.id}`" class="related-card"><img :src="item.image" :alt="item.name" /><p>{{ item.house }}</p><h3>{{ item.name }}</h3></RouterLink></div></section>
    </main>
  </div>
</template>
