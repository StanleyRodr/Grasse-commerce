export type Product = {
  id: number
  name: string
  house: string
  category: string
  scentFamily: string
  occasion: string
  price: number
  rating: number
  reviews: number
  reviewCount?: number
  image: string
  badge?: string
  description?: string
  notes?: string[]
  variants?: ProductVariant[]
}

export type ProductVariant = { id: number; label: string; volumeMl: number; price: number; stock: number }

export type ProductListResponse = {
  data: Product[]
  meta: {
    currentPage: number
    lastPage: number
    total: number
  }
}
