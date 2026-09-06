import { getAuthToken } from './authService'
import type { CartProduct } from '../stores/cart'

type CartResponse = {
  data: {
    items: Array<{ id: number; quantity: number; product: CartProduct }>
  }
}

const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')

const request = async <T>(path: string, init: RequestInit = {}): Promise<T> => {
  const token = getAuthToken()
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...init.headers,
    },
  })

  if (!response.ok) throw new Error(`Cart request failed with status ${response.status}`)
  return response.json() as Promise<T>
}

export const getRemoteCart = () => request<CartResponse>('/cart')
export const replaceRemoteCart = (items: Array<{ product_id: number; quantity: number }>) => request<CartResponse>('/cart', {
  method: 'PUT',
  body: JSON.stringify({ items }),
})
export const addRemoteCartItem = (productId: number, quantity = 1) => request<CartResponse>('/cart/items', {
  method: 'POST',
  body: JSON.stringify({ product_id: productId, quantity }),
})
export const updateRemoteCartItem = (itemId: number, quantity: number) => request<CartResponse>(`/cart/items/${itemId}`, {
  method: 'PATCH',
  body: JSON.stringify({ quantity }),
})
export const removeRemoteCartItem = (itemId: number) => request<CartResponse>(`/cart/items/${itemId}`, { method: 'DELETE' })
