<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
