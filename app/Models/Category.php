<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Category extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['name', 'slug', 'is_active'];
    protected static $logName = 'category';

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
