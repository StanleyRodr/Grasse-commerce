import type { Product, ProductListResponse } from '../types/catalog'

const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')

export type ProductQuery = {
  search?: string
  occasion?: string
  scent_family?: string
  min_rating?: number
  max_price?: number
  sort?: string
  page?: number
  per_page?: number
}

const request = async <T>(path: string): Promise<T> => {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    headers: { Accept: 'application/json' },
  })

  if (!response.ok) {
    throw new Error(`Catalog request failed with status ${response.status}`)
  }

  return response.json() as Promise<T>
}

export const getProducts = (query: ProductQuery = {}): Promise<ProductListResponse> => {
  const params = new URLSearchParams()

  Object.entries(query).forEach(([key, value]) => {
    if (value !== undefined && value !== '') params.set(key, String(value))
  })

  return request<ProductListResponse>(`/products${params.size ? `?${params}` : ''}`)
}

export const getProductById = (id: number): Promise<{ data: Product }> => request<{ data: Product }>(`/products/${id}`)
