<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'product_id', 'product_name', 'product_sku',
        'quantity', 'unit_price', 'cost_price', 'discount', 'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function profit(): float
    {
        return ($this->unit_price - $this->cost_price) * $this->quantity - $this->discount;
    }
}
