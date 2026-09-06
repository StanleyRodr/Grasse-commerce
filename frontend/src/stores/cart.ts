import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { getAuthToken } from '../services/authService'
import { addRemoteCartItem, getRemoteCart, removeRemoteCartItem, updateRemoteCartItem } from '../services/cartService'

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
    if (getAuthToken()) void addRemoteCartItem(product.id).then(hydrate).catch(() => undefined)
  }

  const removeOne = (product: CartProduct) => {
    const index = items.value.findIndex((item) => item.id === product.id && item.name === product.name)
    if (index >= 0) items.value.splice(index, 1)
    void persistRemoteQuantity(product.id)
  }

  const removeAll = (product: CartProduct) => {
    items.value = items.value.filter((item) => !(item.id === product.id && item.name === product.name))
    void persistRemoteQuantity(product.id)
  }

  const persistRemoteQuantity = async (productId: number) => {
    if (!getAuthToken()) return

    try {
      const remote = await getRemoteCart()
      const item = remote.data.items.find((entry) => entry.product.id === productId)
      const quantity = items.value.filter((item) => item.id === productId).length
      if (!item) return
      if (quantity === 0) await removeRemoteCartItem(item.id)
      else await updateRemoteCartItem(item.id, quantity)
    } catch {
      // The local cart remains usable if synchronization fails.
    }
  }

  const hydrate = async () => {
    if (!getAuthToken()) return

    try {
      const response = await getRemoteCart()
      items.value = response.data.items.flatMap((item) => Array.from({ length: item.quantity }, () => item.product))
    } catch {
      // Keep the local cart available when the API is temporarily unavailable.
    }
  }

  return { items, count, add, removeOne, removeAll, hydrate }
})
