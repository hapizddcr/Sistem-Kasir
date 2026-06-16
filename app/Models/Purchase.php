<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Purchase extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['invoice_number', 'supplier_id', 'status', 'total'];
    protected static $logName = 'purchase';

    protected $fillable = [
        'invoice_number', 'supplier_id', 'user_id', 'purchase_date',
        'expected_date', 'received_date', 'status', 'subtotal',
        'discount', 'tax', 'shipping_cost', 'total', 'paid_amount',
        'due_amount', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'received';
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total = $this->subtotal - $this->discount + $this->tax + $this->shipping_cost;
        $this->due_amount = max(0, $this->total - $this->paid_amount);
        $this->save();
    }
}
