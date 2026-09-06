import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { getAuthToken } from '../services/authService'
import { addRemoteCartItem, getRemoteCart, removeRemoteCartItem, replaceRemoteCart, updateRemoteCartItem } from '../services/cartService'

export type CartProduct = {
  id: number
  name: string
  house: string
  price: number
  image: string
  variantId?: number
  variantLabel?: string
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartProduct[]>([])
  const count = computed(() => items.value.length)

  const add = (product: CartProduct) => {
    items.value.push(product)
    if (getAuthToken()) void addRemoteCartItem(product.id, 1, product.variantId).then(hydrate).catch(() => undefined)
  }

  const removeOne = (product: CartProduct) => {
    const index = items.value.findIndex((item) => item.id === product.id && item.variantId === product.variantId)
    if (index >= 0) items.value.splice(index, 1)
    void persistRemoteQuantity(product)
  }

  const removeAll = (product: CartProduct) => {
    items.value = items.value.filter((item) => !(item.id === product.id && item.variantId === product.variantId))
    void persistRemoteQuantity(product)
  }

  const persistRemoteQuantity = async (product: CartProduct) => {
    if (!getAuthToken()) return

    try {
      const remote = await getRemoteCart()
      const item = remote.data.items.find((entry) => entry.product.id === product.id && (entry.variant?.id ?? undefined) === product.variantId)
      const quantity = items.value.filter((item) => item.id === product.id && item.variantId === product.variantId).length
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
      items.value = response.data.items.flatMap((item) => Array.from({ length: item.quantity }, () => ({ ...item.product, variantId: item.variant?.id, variantLabel: item.variant?.label, price: item.variant?.price ?? item.product.price, name: item.variant ? `${item.product.name} · ${item.variant.label}` : item.product.name })))
    } catch {
      // Keep the local cart available when the API is temporarily unavailable.
    }
  }

  const syncRemote = async () => {
    if (!getAuthToken()) return

    const quantities = new Map<string, { product_id: number; variant_id?: number; quantity: number }>()
    items.value.forEach((item) => { const key = `${item.id}:${item.variantId ?? 0}`; const current = quantities.get(key); quantities.set(key, { product_id: item.id, variant_id: item.variantId, quantity: (current?.quantity ?? 0) + 1 }) })
    await replaceRemoteCart([...quantities.values()])
  }

  return { items, count, add, removeOne, removeAll, hydrate, syncRemote }
})
