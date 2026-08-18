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
  image: string
  badge?: string
}

export type ProductListResponse = {
  data: Product[]
  meta: {
    currentPage: number
    lastPage: number
    total: number
  }
}
