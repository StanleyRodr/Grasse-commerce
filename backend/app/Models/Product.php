<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'house',
        'category',
        'scent_family',
        'occasion',
        'price',
        'rating',
        'reviews_count',
        'image',
        'badge',
        'description',
        'notes',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'rating' => 'float',
            'reviews_count' => 'integer',
            'notes' => 'array',
            'stock' => 'integer',
        ];
    }

    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }
}
