export type AuthUser = {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  role: 'customer' | 'admin'
}

type AuthResponse = {
  user: AuthUser
  token: string
}

const apiBaseUrl = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')
const tokenKey = 'grasse_auth_token'
const userKey = 'grasse_auth_user'

const request = async (path: string, body: Record<string, string>): Promise<AuthResponse> => {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify(body),
  })

  const payload = await response.json() as AuthResponse & { message?: string; errors?: Record<string, string[]> }
  if (!response.ok) throw new Error(payload.message ?? 'No pudimos completar la solicitud.')

  return payload
}

export const login = (email: string, password: string) => request('/auth/login', { email, password })

export const register = (name: string, email: string, password: string, passwordConfirmation: string) => request('/auth/register', {
  name,
  email,
  password,
  password_confirmation: passwordConfirmation,
})

export const forgotPassword = (email: string) => request('/auth/password/forgot', { email })

export const resetPassword = (token: string, email: string, password: string, passwordConfirmation: string) => request('/auth/password/reset', {
  token,
  email,
  password,
  password_confirmation: passwordConfirmation,
})

export const logout = async () => {
  const token = getAuthToken()
  try {
    if (token) {
      await fetch(`${apiBaseUrl}/auth/logout`, {
        method: 'POST',
        headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
      })
    }
  } finally {
    clearAuthToken()
  }
}

export const saveAuthToken = (token: string) => localStorage.setItem(tokenKey, token)
export const getAuthToken = () => localStorage.getItem(tokenKey)
export const saveAuthSession = (response: AuthResponse) => {
  saveAuthToken(response.token)
  localStorage.setItem(userKey, JSON.stringify(response.user))
}
export const getCurrentUser = async (): Promise<AuthUser | null> => {
  const token = getAuthToken()
  if (!token) return null

  const response = await fetch(`${apiBaseUrl}/auth/me`, { headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } })
  if (!response.ok) return null
  const payload = await response.json() as { user: AuthUser }
  localStorage.setItem(userKey, JSON.stringify(payload.user))
  return payload.user
}
export const getAuthUser = (): AuthUser | null => {
  const value = localStorage.getItem(userKey)
  if (!value) return null

  try {
    return JSON.parse(value) as AuthUser
  } catch {
    localStorage.removeItem(userKey)
    return null
  }
}
export const clearAuthToken = () => {
  localStorage.removeItem(tokenKey)
  localStorage.removeItem(userKey)
}
