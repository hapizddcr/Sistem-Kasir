<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static $logAttributes = ['name', 'sku', 'selling_price', 'stock', 'is_active'];
    protected static $logName = 'product';

    protected $fillable = [
        'sku', 'barcode', 'name', 'slug', 'category_id', 'unit_id',
        'description', 'purchase_price', 'selling_price', 'wholesale_price',
        'stock', 'min_stock', 'weight', 'has_variation', 'track_stock',
        'is_active', 'is_taxable',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'has_variation' => 'boolean',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_taxable' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $image = $this->primaryImage();
        if ($image && file_exists(public_path('storage/' . $image->path))) {
            return asset('storage/' . $image->path);
        }
        return asset('images/no-product.svg');
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock <= $this->min_stock;
    }

    public function profitMargin(): float
    {
        if ($this->purchase_price <= 0) return 0;
        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }
}
