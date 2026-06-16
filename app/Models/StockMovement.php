<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class StockMovement extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['product_id', 'type', 'quantity'];
    protected static $logName = 'stock_movement';

    protected $fillable = [
        'product_id', 'type', 'quantity', 'stock_before', 'stock_after',
        'unit_cost', 'reference_type', 'reference_id', 'notes', 'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function isInflow(): bool
    {
        return in_array($this->type, ['in', 'purchase', 'return', 'opname']) && $this->quantity > 0;
    }

    public function isOutflow(): bool
    {
        return in_array($this->type, ['out', 'sale', 'adjustment']) && $this->quantity > 0;
    }
}
