<script setup lang="ts">
import { computed, ref } from 'vue'
import { ArrowRight, ChevronDown, Menu, Search, ShoppingBag, SlidersHorizontal, Sparkles, UserRound, X } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import ProductCard from '../components/ProductCard.vue'
import { useCartStore } from '../stores/cart'
import { getProducts } from '../services/catalogService'
import type { Product } from '../types/catalog'

const products: Product[] = getProducts().data
const categories = ['Todos', 'Floral', 'Madera', 'Especiada', 'Amaderada']
const activeCategory = ref('Todos')
const searchQuery = ref('')
const isMenuOpen = ref(false)
const likedProducts = ref<number[]>([])
const cart = useCartStore()
const addingProductId = ref<number | null>(null)
const toastMessage = ref('')
const cartPulse = ref(false)
const filteredProducts = computed(() => products.filter((product) => {
  const categoryMatch = activeCategory.value === 'Todos' || product.category === activeCategory.value
  const query = searchQuery.value.toLowerCase().trim()
  return categoryMatch && (!query || `${product.name} ${product.house}`.toLowerCase().includes(query))
}))
const toggleLike = (id: number) => { likedProducts.value = likedProducts.value.includes(id) ? likedProducts.value.filter((item) => item !== id) : [...likedProducts.value, id] }
const focusSearch = () => { document.querySelector<HTMLInputElement>('.search-input')?.focus() }
const addToCart = (product: Product) => { cart.add(product); addingProductId.value = product.id; cartPulse.value = true; toastMessage.value = `${product.name} se añadió al carrito`; window.setTimeout(() => { addingProductId.value = null }, 1200); window.setTimeout(() => { cartPulse.value = false }, 650); window.setTimeout(() => { toastMessage.value = '' }, 2600) }
</script>

<template>
  <div class="site-shell"><div class="announcement"><span>Envío gratis en compras mayores a $1,500 MXN</span><span class="announcement-detail">Descubre nuestra selección curada <ArrowRight :size="14" /></span></div><header class="header"><button class="mobile-menu" aria-label="Abrir menú" @click="isMenuOpen = !isMenuOpen"><X v-if="isMenuOpen" :size="21" /><Menu v-else :size="21" /></button><a class="wordmark" href="#top">grasse<span>.</span></a><nav class="main-nav" :class="{ 'is-open': isMenuOpen }"><a href="#catalogo">Perfumes</a><a href="#catalogo">Novedades</a><a href="#catalogo">Marcas</a><a href="#historia">Nuestra historia</a></nav><div class="header-actions"><button class="icon-button" aria-label="Buscar" @click="focusSearch"><Search :size="20" /></button><RouterLink to="/cuenta" class="icon-button account-button" aria-label="Mi cuenta"><UserRound :size="20" /><span>Cuenta</span></RouterLink><RouterLink to="/carrito" class="icon-button bag-button" :class="{ 'cart-pulse': cartPulse }" aria-label="Carrito"><ShoppingBag :size="20" /><span v-if="cart.count" class="cart-count">{{ cart.count }}</span></RouterLink></div></header><main id="top"><section class="hero"><div class="hero-copy"><p class="eyebrow"><Sparkles :size="15" /> La esencia de lo extraordinario</p><h1>Encuentra la<br /><em>esencia</em> que te define.</h1><p class="hero-text">Una selección íntima de perfumes excepcionales. Historias que se llevan en la piel.</p><a href="#catalogo" class="primary-button">Explorar colección <ArrowRight :size="17" /></a></div><div class="hero-art" aria-label="Frasco de perfume sobre un fondo cálido"><div class="sun-disc"></div><div class="hero-bottle"><div class="bottle-cap"></div><div class="bottle-label">GRASSE<br /><small>PARFUM</small></div></div><span class="art-note note-one">01</span><span class="art-note note-two">PARFUMERIE</span></div></section><section id="catalogo" class="catalog-section"><div class="section-heading"><div><p class="eyebrow">Nuestra colección</p><h2>Favoritos de la casa</h2></div><a href="#catalogo" class="text-link">Ver todos <ArrowRight :size="15" /></a></div><div class="catalog-tools"><div class="category-list"><button v-for="category in categories" :key="category" :class="{ active: activeCategory === category }" @click="activeCategory = category">{{ category }}</button></div><div class="tool-actions"><label class="search-box"><Search :size="17" /><input v-model="searchQuery" class="search-input" type="search" placeholder="Buscar perfume..." /></label><button class="filter-button"><SlidersHorizontal :size="16" /> Filtros <ChevronDown :size="15" /></button></div></div><div class="product-grid"><ProductCard v-for="product in filteredProducts" :key="product.id" :product="product" :liked="likedProducts.includes(product.id)" :adding="addingProductId === product.id" @toggle-like="toggleLike" @add="addToCart" /></div><p v-if="!filteredProducts.length" class="empty-state">No encontramos perfumes con esa búsqueda.</p></section><section id="historia" class="story-section"><div class="story-line"></div><p class="eyebrow">El arte de elegir</p><h2>Perfumes con una<br /><em>historia</em> que contar.</h2><p>Creemos que un perfume no es solo una fragancia. Es memoria, presencia y la forma más sutil de dejar huella.</p><a href="#historia" class="text-link">Conoce Grasse <ArrowRight :size="15" /></a></section></main><footer class="footer"><a class="wordmark" href="#top">grasse<span>.</span></a><p>Una forma de sentir.</p><div><a href="#catalogo">Contacto</a><a href="#catalogo">Envíos</a><a href="#catalogo">Privacidad</a></div></footer><Transition name="toast"><div v-if="toastMessage" class="cart-toast"><ShoppingBag :size="17" /><span>{{ toastMessage }}</span></div></Transition></div>
</template>
