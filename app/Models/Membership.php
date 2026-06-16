<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Membership extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['name', 'discount_percent'];
    protected static $logName = 'membership';

    protected $fillable = [
        'name', 'slug', 'min_spent', 'discount_percent',
        'points_multiplier', 'color', 'description', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'min_spent' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'points_multiplier' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
