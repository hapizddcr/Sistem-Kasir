<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['name', 'code', 'phone'];
    protected static $logName = 'customer';

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'city',
        'gender', 'birth_date', 'membership_id', 'points',
        'total_spent', 'total_transactions', 'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'points' => 'integer',
        'total_spent' => 'decimal:2',
        'total_transactions' => 'integer',
        'is_active' => 'boolean',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }

    public function usePoints(int $points): bool
    {
        if ($this->points < $points) return false;
        $this->decrement('points', $points);
        return true;
    }

    public function recordTransaction(float $amount): void
    {
        $this->increment('total_transactions');
        $this->increment('total_spent', $amount);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
