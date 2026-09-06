import { getAuthToken } from './authService'

type ReviewResponse = { data: { data: Array<{ id: number; rating: number; comment: string; verified_purchase: boolean; user: { name: string }; created_at: string }> } }
const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')

const request = async <T>(path: string, init: RequestInit = {}): Promise<T> => {
  const token = getAuthToken()
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(token ? { Authorization: `Bearer ${token}` } : {}), ...init.headers },
  })
  if (!response.ok) throw new Error(`Review request failed with status ${response.status}`)
  return response.json() as Promise<T>
}

export const getReviews = (productId: number) => request<ReviewResponse>(`/products/${productId}/reviews`)
export const createReview = (productId: number, rating: number, comment: string) => request(`/products/${productId}/reviews`, { method: 'POST', body: JSON.stringify({ rating, comment }) })

export const getMyReviews = () => request<{ data: Array<{ id: number; rating: number; comment: string; verified_purchase: boolean; created_at: string; product: { id: number; name: string; house: string; image: string } }> }>('/auth/reviews')
