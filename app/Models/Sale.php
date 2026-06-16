<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Sale extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['invoice_number', 'status', 'total'];
    protected static $logName = 'sale';

    protected $fillable = [
        'invoice_number', 'customer_id', 'user_id', 'sale_date', 'status',
        'subtotal', 'discount', 'tax', 'total', 'paid_amount',
        'change_amount', 'points_earned', 'points_used', 'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'points_earned' => 'integer',
        'points_used' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function totalItems(): int
    {
        return $this->items->sum('quantity');
    }

    public function totalCost(): float
    {
        return $this->items->sum(fn($item) => $item->cost_price * $item->quantity);
    }

    public function totalProfit(): float
    {
        return $this->total - $this->totalCost();
    }
}
