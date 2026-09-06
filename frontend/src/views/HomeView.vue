<script setup lang="ts">
 import { computed, onMounted, ref, watch } from 'vue'
import { ArrowRight, ChevronDown, Menu, Search, ShoppingBag, SlidersHorizontal, Sparkles, UserRound, X } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import ProductCard from '../components/ProductCard.vue'
import { useCartStore } from '../stores/cart'
import { getProducts } from '../services/catalogService'
import type { Product } from '../types/catalog'
import { getAuthToken } from '../services/authService'
import { addToWishlist, getWishlist, removeFromWishlist } from '../services/wishlistService'

 const products = ref<Product[]>([])
 const isLoading = ref(true)
 const loadError = ref('')
const categories = ['Todos', 'Fresco', 'Diario', 'Fiesta', 'Noche']
const activeCategory = ref('Todos')
const searchQuery = ref('')
const sortBy = ref('featured')
const showFilters = ref(false)
const selectedScent = ref('Todos')
const minRating = ref(0)
const maxPrice = ref(6000)
const currentPage = ref(1)
const pageSize = 4
const isMenuOpen = ref(false)
const likedProducts = ref<number[]>([])
const cart = useCartStore()
const addingProductId = ref<number | null>(null)
const toastMessage = ref('')
const cartPulse = ref(false)
 const filteredProducts = computed(() => products.value.filter((product) => {
  const categoryMatch = activeCategory.value === 'Todos' || product.occasion === activeCategory.value
  const scentMatch = selectedScent.value === 'Todos' || product.scentFamily === selectedScent.value
  const query = searchQuery.value.toLowerCase().trim()
  return categoryMatch && scentMatch && (!query || `${product.name} ${product.house}`.toLowerCase().includes(query)) && product.rating >= minRating.value && product.price <= maxPrice.value
}))
const sortedProducts = computed(() => [...filteredProducts.value].sort((a, b) => sortBy.value === 'price-low' ? a.price - b.price : sortBy.value === 'price-high' ? b.price - a.price : sortBy.value === 'rating' ? b.rating - a.rating : a.id - b.id))
const totalPages = computed(() => Math.max(1, Math.ceil(sortedProducts.value.length / pageSize)))
const paginatedProducts = computed(() => sortedProducts.value.slice((currentPage.value - 1) * pageSize, currentPage.value * pageSize))
watch([activeCategory, searchQuery, sortBy, selectedScent, minRating, maxPrice], () => { currentPage.value = 1 })
const clearFilters = () => { activeCategory.value = 'Todos'; selectedScent.value = 'Todos'; minRating.value = 0; maxPrice.value = 6000 }
const toggleLike = (id: number) => {
  const liked = likedProducts.value.includes(id)
  likedProducts.value = liked ? likedProducts.value.filter((item) => item !== id) : [...likedProducts.value, id]
  if (!getAuthToken()) return
  void (liked ? removeFromWishlist(id) : addToWishlist(id)).catch(() => undefined)
}
const focusSearch = () => { document.querySelector<HTMLInputElement>('.search-input')?.focus() }
 const addToCart = (product: Product) => { cart.add(product); addingProductId.value = product.id; cartPulse.value = true; toastMessage.value = `${product.name} se añadió al carrito`; window.setTimeout(() => { addingProductId.value = null }, 1200); window.setTimeout(() => { cartPulse.value = false }, 650); window.setTimeout(() => { toastMessage.value = '' }, 2600) }
const loadCatalog = async () => {
   isLoading.value = true
   loadError.value = ''

   try {
     const response = await getProducts({ per_page: 24 })
     products.value = response.data
   } catch {
     loadError.value = 'No pudimos cargar la colección. Intenta de nuevo.'
   } finally {
     isLoading.value = false
   }
}
onMounted(() => {
  void loadCatalog()
  void cart.hydrate()
  if (getAuthToken()) void getWishlist().then((response) => { likedProducts.value = response.data.map((item) => item.product.id) }).catch(() => undefined)
})
</script>

<template>
  <div class="site-shell"><div class="announcement"><span>Envío gratis en compras mayores a $1,500 MXN</span><span class="announcement-detail">Descubre nuestra selección curada <ArrowRight :size="14" /></span></div><header class="header"><button class="mobile-menu" aria-label="Abrir menú" @click="isMenuOpen = !isMenuOpen"><X v-if="isMenuOpen" :size="21" /><Menu v-else :size="21" /></button><a class="wordmark" href="#top">grasse<span>.</span></a><nav class="main-nav" :class="{ 'is-open': isMenuOpen }"><a href="#catalogo">Perfumes</a><a href="#catalogo">Novedades</a><a href="#catalogo">Marcas</a><a href="#historia">Nuestra historia</a></nav><div class="header-actions"><button class="icon-button" aria-label="Buscar" @click="focusSearch"><Search :size="20" /></button><RouterLink to="/cuenta" class="icon-button account-button" aria-label="Mi cuenta"><UserRound :size="20" /><span>Cuenta</span></RouterLink><RouterLink to="/carrito" class="icon-button bag-button" :class="{ 'cart-pulse': cartPulse }" aria-label="Carrito"><ShoppingBag :size="20" /><span v-if="cart.count" class="cart-count">{{ cart.count }}</span></RouterLink></div></header><main id="top"><section class="hero"><div class="hero-copy"><p class="eyebrow"><Sparkles :size="15" /> La esencia de lo extraordinario</p><h1>Encuentra la<br /><em>esencia</em> que te define.</h1><p class="hero-text">Una selección íntima de perfumes excepcionales. Historias que se llevan en la piel.</p><a href="#catalogo" class="primary-button">Explorar colección <ArrowRight :size="17" /></a></div><div class="hero-art" aria-label="Frasco de perfume sobre un fondo cálido"><div class="sun-disc"></div><div class="hero-bottle"><div class="bottle-cap"></div><div class="bottle-label">GRASSE<br /><small>PARFUM</small></div></div><span class="art-note note-one">01</span><span class="art-note note-two">PARFUMERIE</span></div></section><section id="catalogo" class="catalog-section"><div class="section-heading"><div><p class="eyebrow">Nuestra colección</p><h2>Favoritos de la casa</h2></div><a href="#catalogo" class="text-link">Ver todos <ArrowRight :size="15" /></a></div><div class="catalog-tools"><div class="category-list"><button v-for="category in categories" :key="category" :class="{ active: activeCategory === category }" @click="activeCategory = category">{{ category }}</button></div><div class="tool-actions"><label class="search-box"><Search :size="17" /><input v-model="searchQuery" class="search-input" type="search" placeholder="Buscar perfume..." /></label><label class="sort-box">Ordenar<select v-model="sortBy"><option value="featured">Destacados</option><option value="rating">Mejor calificados</option><option value="price-low">Precio menor</option><option value="price-high">Precio mayor</option></select></label><button class="filter-button" :class="{ active: showFilters }" @click="showFilters = !showFilters"><SlidersHorizontal :size="16" /> Filtros <ChevronDown :size="15" /></button></div></div><div v-if="showFilters" class="filter-panel filter-panel-inline"><label>Familia olfativa<select v-model="selectedScent"><option>Todos</option><option>Fresca</option><option>Floral</option><option>Citrica</option><option>Amaderada</option><option>Almizclada</option><option>Ambarada</option></select></label><label>Calificación mínima<select v-model.number="minRating"><option :value="0">Todas</option><option :value="4">4+ estrellas</option><option :value="4.5">4.5+ estrellas</option><option :value="4.8">4.8+ estrellas</option></select></label><label>Precio máximo<select v-model.number="maxPrice"><option :value="6000">Cualquier precio</option><option :value="3500">Hasta $3,500</option><option :value="4500">Hasta $4,500</option><option :value="5000">Hasta $5,000</option></select></label><button class="clear-filter" @click="clearFilters">Limpiar filtros</button></div><div class="product-grid"><ProductCard v-for="product in paginatedProducts" :key="product.id" :product="product" :liked="likedProducts.includes(product.id)" :adding="addingProductId === product.id" @toggle-like="toggleLike" @add="addToCart" /></div><p v-if="!filteredProducts.length" class="empty-state">No encontramos perfumes con esos filtros.</p><div v-if="totalPages > 1" class="pagination"><button v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }" @click="currentPage = page">{{ page }}</button></div></section><section id="historia" class="story-section"><div class="story-line"></div><p class="eyebrow">El arte de elegir</p><h2>Perfumes con una<br /><em>historia</em> que contar.</h2><p>Creemos que un perfume no es solo una fragancia. Es memoria, presencia y la forma más sutil de dejar huella.</p><a href="#historia" class="text-link">Conoce Grasse <ArrowRight :size="15" /></a></section></main><footer class="footer"><a class="wordmark" href="#top">grasse<span>.</span></a><p>Una forma de sentir.</p><div><a href="#catalogo">Contacto</a><a href="#catalogo">Envíos</a><a href="#catalogo">Privacidad</a></div></footer><Transition name="toast"><div v-if="toastMessage" class="cart-toast"><ShoppingBag :size="17" /><span>{{ toastMessage }}</span></div></Transition></div>
</template>
