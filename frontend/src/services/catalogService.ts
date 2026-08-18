import type { Product, ProductListResponse } from '../types/catalog'

const products: Product[] = [
  { id: 1, name: 'Santal 33', house: 'Le Labo', category: 'Madera', scentFamily: 'Amaderada', occasion: 'Diario', price: 4250, rating: 4.9, reviews: 128, badge: 'Más vendido', image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=85' },
  { id: 2, name: 'Another 13', house: 'Le Labo', category: 'Amaderada', scentFamily: 'Almizclada', occasion: 'Diario', price: 3980, rating: 4.8, reviews: 94, image: 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=85' },
  { id: 3, name: 'Gris Charnel', house: 'BDK Parfums', category: 'Especiada', scentFamily: 'Ambarada', occasion: 'Noche', price: 3120, rating: 4.7, reviews: 76, badge: 'Nuevo', image: 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?auto=format&fit=crop&w=900&q=85' },
  { id: 4, name: 'Bal d’Afrique', house: 'Byredo', category: 'Floral', scentFamily: 'Citrica', occasion: 'Fiesta', price: 4890, rating: 4.9, reviews: 211, image: 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?auto=format&fit=crop&w=900&q=85' },
  { id: 5, name: 'Mojave Ghost', house: 'Byredo', category: 'Floral', scentFamily: 'Floral', occasion: 'Diario', price: 4650, rating: 4.6, reviews: 63, image: 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=85' },
  { id: 6, name: 'The Noir 29', house: 'Le Labo', category: 'Madera', scentFamily: 'Amaderada', occasion: 'Noche', price: 4350, rating: 4.8, reviews: 89, image: 'https://images.unsplash.com/photo-1615634260167-c8cd3cc7ad4a?auto=format&fit=crop&w=900&q=85' },
  { id: 7, name: 'Musc Ravageur', house: 'Frederic Malle', category: 'Especiada', scentFamily: 'Almizclada', occasion: 'Fiesta', price: 5180, rating: 4.5, reviews: 47, image: 'https://images.unsplash.com/photo-1610461888750-10b4a0c4d3a1?auto=format&fit=crop&w=900&q=85' },
  { id: 8, name: 'Gypsy Water', house: 'Byredo', category: 'Amaderada', scentFamily: 'Fresca', occasion: 'Fresco', price: 4420, rating: 4.7, reviews: 102, image: 'https://images.unsplash.com/photo-1590736704728-f4730bb30770?auto=format&fit=crop&w=900&q=85' },
]

export const getProducts = (): ProductListResponse => ({ data: products, meta: { currentPage: 1, lastPage: 1, total: products.length } })
export const getProductById = (id: number) => products.find((product) => product.id === id)
