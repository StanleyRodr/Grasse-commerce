import { getAuthToken } from './authService'

const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')
const request = async <T>(path: string, init: RequestInit = {}): Promise<T> => {
  const response = await fetch(`${apiBaseUrl}${path}`, { ...init, headers: { Accept: 'application/json', 'Content-Type': 'application/json', Authorization: `Bearer ${getAuthToken() ?? ''}`, ...init.headers } })
  if (!response.ok) throw new Error('No se pudo cargar el panel administrativo.')
  return response.json() as Promise<T>
}

export type AdminOrder = { id: number; status: string; total: number; created_at: string; user: { name: string; email: string }; items: Array<{ product_name: string; variant_label?: string; quantity: number }> }
export type AdminOverview = { users: number; products: number; orders: number; sales: number; averageOrder: number }
export const getAdminOverview = () => request<{ data: AdminOverview }>('/admin/overview')
export const getAdminOrders = () => request<{ data: { data: AdminOrder[] } }>('/admin/orders')
export const updateAdminOrderStatus = (id: number, status: string) => request<{ data: AdminOrder }>(`/admin/orders/${id}/status`, { method: 'PATCH', body: JSON.stringify({ status }) })
export type AdminProduct = { id: number; name: string; house: string; price: number; stock: number; image: string }
export const getAdminProducts = () => request<{ data: AdminProduct[] }>('/products?per_page=24')
export const createAdminProduct = (product: Record<string, unknown>) => request<{ data: AdminProduct }>('/admin/products', { method: 'POST', body: JSON.stringify(product) })
export const updateAdminProduct = (id: number, product: Record<string, unknown>) => request<{ data: AdminProduct }>(`/admin/products/${id}`, { method: 'PATCH', body: JSON.stringify(product) })
