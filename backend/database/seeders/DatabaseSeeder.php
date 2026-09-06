<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $products = [
            ['name' => 'Santal 33', 'house' => 'Le Labo', 'category' => 'Madera', 'scent_family' => 'Amaderada', 'occasion' => 'Diario', 'price' => 4250, 'rating' => 4.9, 'reviews_count' => 128, 'badge' => 'Más vendido', 'description' => 'Una composición que habla de madera, fuego y libertad. Un clásico moderno con carácter inconfundible.', 'notes' => ['Cardamomo', 'Iris', 'Madera de cedro'], 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Another 13', 'house' => 'Le Labo', 'category' => 'Amaderada', 'scent_family' => 'Almizclada', 'occasion' => 'Diario', 'price' => 3980, 'rating' => 4.8, 'reviews_count' => 94, 'description' => 'Una piel limpia y magnética, con ambreta, jazmín y musgo que se funden de forma casi imperceptible.', 'notes' => ['Ambreta', 'Jazmín', 'Musgo'], 'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Gris Charnel', 'house' => 'BDK Parfums', 'category' => 'Especiada', 'scent_family' => 'Ambarada', 'occasion' => 'Noche', 'price' => 3120, 'rating' => 4.7, 'reviews_count' => 76, 'badge' => 'Nuevo', 'description' => 'Una fragancia voluptuosa y adictiva donde el higo, el té negro y el sándalo se encuentran en una composición cremosa.', 'notes' => ['Higo', 'Té negro', 'Sándalo'], 'image' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Bal d’Afrique', 'house' => 'Byredo', 'category' => 'Floral', 'scent_family' => 'Citrica', 'occasion' => 'Fiesta', 'price' => 4890, 'rating' => 4.9, 'reviews_count' => 211, 'description' => 'Una celebración luminosa de París y África, con neroli, cedro y vetiver en equilibrio.', 'notes' => ['Neroli', 'Jazmín', 'Vetiver'], 'image' => 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Mojave Ghost', 'house' => 'Byredo', 'category' => 'Floral', 'scent_family' => 'Floral', 'occasion' => 'Diario', 'price' => 4650, 'rating' => 4.6, 'reviews_count' => 63, 'description' => 'Una fragancia floral y etérea, suave como una brisa sobre el desierto.', 'notes' => ['Ambreta', 'Violeta', 'Sándalo'], 'image' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'The Noir 29', 'house' => 'Le Labo', 'category' => 'Madera', 'scent_family' => 'Amaderada', 'occasion' => 'Noche', 'price' => 4350, 'rating' => 4.8, 'reviews_count' => 89, 'description' => 'Una interpretación profunda y elegante de la hoja de higuera y el té negro.', 'notes' => ['Higo', 'Té negro', 'Laurel'], 'image' => 'https://images.unsplash.com/photo-1615634260167-c8cd3cc7ad4a?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Musc Ravageur', 'house' => 'Frederic Malle', 'category' => 'Especiada', 'scent_family' => 'Almizclada', 'occasion' => 'Fiesta', 'price' => 5180, 'rating' => 4.5, 'reviews_count' => 47, 'description' => 'Un perfume cálido y sensual de especias, vainilla y almizcle.', 'notes' => ['Canela', 'Vainilla', 'Almizcle'], 'image' => 'https://images.unsplash.com/photo-1610461888750-10b4a0c4d3a1?auto=format&fit=crop&w=900&q=85'],
            ['name' => 'Gypsy Water', 'house' => 'Byredo', 'category' => 'Amaderada', 'scent_family' => 'Fresca', 'occasion' => 'Fresco', 'price' => 4420, 'rating' => 4.7, 'reviews_count' => 102, 'description' => 'Una composición fresca y amaderada inspirada en la libertad y la naturaleza.', 'notes' => ['Limón', 'Pimienta', 'Pino'], 'image' => 'https://images.unsplash.com/photo-1590736704728-f4730bb30770?auto=format&fit=crop&w=900&q=85'],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(['name' => $product['name'], 'house' => $product['house']], $product);
        }
    }
}
