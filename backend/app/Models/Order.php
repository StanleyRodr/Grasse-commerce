<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'address_id', 'guest_email', 'guest_name', 'guest_address', 'status', 'subtotal', 'shipping', 'total'];
    protected function casts(): array { return ['subtotal' => 'float', 'shipping' => 'float', 'total' => 'float', 'guest_address' => 'array']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function address(): BelongsTo { return $this->belongsTo(Address::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
}
