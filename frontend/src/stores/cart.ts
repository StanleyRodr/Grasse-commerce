import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

export type CartProduct = {
  id: number
  name: string
  house: string
  price: number
  image: string
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartProduct[]>([])
  const count = computed(() => items.value.length)

  const add = (product: CartProduct) => {
    items.value.push(product)
  }

  const removeOne = (product: CartProduct) => {
    const index = items.value.findIndex((item) => item.id === product.id && item.name === product.name)
    if (index >= 0) items.value.splice(index, 1)
  }

  const removeAll = (product: CartProduct) => {
    items.value = items.value.filter((item) => !(item.id === product.id && item.name === product.name))
  }

  return { items, count, add, removeOne, removeAll }
})
