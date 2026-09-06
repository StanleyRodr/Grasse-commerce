import { getAuthToken } from './authService'
import type { Product } from '../types/catalog'

type WishlistResponse = { data: Array<{ product: Pick<Product, 'id' | 'name' | 'house' | 'price' | 'image'> }> }
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
  if (!response.ok) throw new Error(`Wishlist request failed with status ${response.status}`)
  return response.json() as Promise<T>
}

export const getWishlist = () => request<WishlistResponse>('/wishlist')
export const addToWishlist = (productId: number) => request(`/wishlist`, { method: 'POST', body: JSON.stringify({ product_id: productId }) })
export const removeFromWishlist = (productId: number) => request(`/wishlist/${productId}`, { method: 'DELETE' })
