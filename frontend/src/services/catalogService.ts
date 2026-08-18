import type { Product, ProductListResponse } from '../types/catalog'

const products: Product[] = [
  { id: 1, name: 'Santal 33', house: 'Le Labo', category: 'Madera', price: 4250, rating: 4.9, reviews: 128, badge: 'Más vendido', image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=85' },
  { id: 2, name: 'Another 13', house: 'Le Labo', category: 'Amaderada', price: 3980, rating: 4.8, reviews: 94, image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=85' },
  { id: 3, name: 'Gris Charnel', house: 'BDK Parfums', category: 'Especiada', price: 3120, rating: 4.7, reviews: 76, badge: 'Nuevo', image: 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?auto=format&fit=crop&w=900&q=85' },
  { id: 4, name: 'Bal d’Afrique', house: 'Byredo', category: 'Floral', price: 4890, rating: 4.9, reviews: 211, image: 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?auto=format&fit=crop&w=900&q=85' },
]

export const getProducts = (): ProductListResponse => ({
  data: products,
  meta: { currentPage: 1, lastPage: 1, total: products.length },
})

export const getProductById = (id: number) => products.find((product) => product.id === id)
