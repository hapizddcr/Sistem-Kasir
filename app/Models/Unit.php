<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Unit extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['name', 'short_name', 'is_active'];
    protected static $logName = 'unit';

    protected $fillable = ['name', 'short_name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
