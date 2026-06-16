<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Supplier extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['name', 'code', 'phone', 'is_active'];
    protected static $logName = 'supplier';

    protected $fillable = [
        'code', 'name', 'contact_person', 'phone', 'email', 'address',
        'city', 'province', 'postal_code', 'npwp', 'notes', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
