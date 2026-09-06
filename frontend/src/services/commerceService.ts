import { getAuthToken } from './authService'

const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')
const request = async <T>(path: string, init: RequestInit = {}): Promise<T> => {
  const token = getAuthToken()
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(token ? { Authorization: `Bearer ${token}` } : {}), ...init.headers },
  })
  const payload = await response.json() as T & { message?: string }
  if (!response.ok) throw new Error(payload.message ?? 'No pudimos completar la operación.')
  return payload
}

export type AddressInput = { label: string; recipient: string; line1: string; city: string; state: string; postal_code: string; phone?: string; is_default: boolean }
export type Address = AddressInput & { id: number }
export const getAddresses = () => request<{ data: Address[] }>('/addresses')
export const createAddress = (address: AddressInput) => request<{ data: { id: number } }>('/addresses', { method: 'POST', body: JSON.stringify(address) })
export const setDefaultAddress = (id: number) => request<{ data: Address }>(`/addresses/${id}/default`, { method: 'POST' })
export const deleteAddress = (id: number) => request<{ message: string }>(`/addresses/${id}`, { method: 'DELETE' })
export const createOrder = (addressId: number) => request<{ data: { id: number; status: string } }>('/orders', { method: 'POST', body: JSON.stringify({ address_id: addressId }) })
export const createCheckoutSession = (orderId: number) => request<{ data: { id: string; url: string } }>(`/orders/${orderId}/checkout`, { method: 'POST' })
export const getOrders = () => request<{ data: { data: Array<{ id: number; status: string; total: number; created_at: string; items: Array<{ product_name: string; quantity: number }> }> } }>('/orders')
