<script setup lang="ts">
import { Heart, ShoppingBag } from '@lucide/vue'
import { RouterLink } from 'vue-router'
import type { Product } from '../types/catalog'

defineProps<{ product: Product; liked: boolean; adding: boolean }>()
const emit = defineEmits<{ toggleLike: [id: number]; add: [product: Product] }>()
</script>

<template>
  <article class="product-card"><div class="product-image-wrap"><RouterLink :to="`/producto/${product.id}`" class="product-link"><img :src="product.image" :alt="product.name" class="product-image" /></RouterLink><span v-if="product.badge" class="product-badge">{{ product.badge }}</span><button class="heart-button" :class="{ liked }" :aria-label="`Añadir ${product.name} a favoritos`" @click.stop="emit('toggleLike', product.id)"><Heart :size="18" :fill="liked ? 'currentColor' : 'none'" /></button></div><RouterLink :to="`/producto/${product.id}`" class="product-link product-info"><div><p class="product-house">{{ product.house }}</p><h3>{{ product.name }}</h3></div><p class="product-price">${{ product.price.toLocaleString('es-MX') }}</p></RouterLink><div class="product-meta"><span class="rating">★ {{ product.rating }} <small>({{ product.reviews }})</small></span><button class="add-button" :class="{ 'is-added': adding }" @click="emit('add', product)">{{ adding ? 'Agregado' : 'Agregar' }} <ShoppingBag :size="15" /></button></div></article>
</template>
