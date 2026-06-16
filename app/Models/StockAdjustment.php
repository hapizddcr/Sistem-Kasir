<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'user_id', 'adjustment_date', 'reason', 'notes', 'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
