<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'label', 'volume_ml', 'price', 'stock'];
    protected function casts(): array { return ['volume_ml' => 'integer', 'price' => 'float', 'stock' => 'integer']; }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
