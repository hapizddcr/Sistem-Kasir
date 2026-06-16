<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Record a stock movement and update product stock.
     */
    public function record(
        Product $product,
        string $type,
        int $quantity,
        ?string $notes = null,
        ?float $unitCost = null,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): StockMovement {
        if ($quantity === 0) {
            throw new RuntimeException('Quantity cannot be zero');
        }

        return DB::transaction(function () use ($product, $type, $quantity, $notes, $unitCost, $userId, $referenceType, $referenceId) {
            $stockBefore = $product->stock;

            $signedQuantity = match ($type) {
                'in', 'purchase', 'return' => abs($quantity),
                'out', 'sale' => -abs($quantity),
                'adjustment' => $quantity, // can be positive or negative
                'opname' => $quantity,
                default => throw new RuntimeException("Invalid stock type: $type"),
            };

            $stockAfter = $stockBefore + $signedQuantity;

            if ($stockAfter < 0 && $product->track_stock) {
                throw new RuntimeException("Stok tidak cukup untuk produk: {$product->name}. Stok tersedia: {$stockBefore}");
            }

            $product->stock = $stockAfter;
            $product->save();

            return StockMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $signedQuantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => $unitCost ?? $product->purchase_price,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Process bulk stock movements (e.g., for purchase receive).
     */
    public function bulkRecord(array $movements): void
    {
        DB::transaction(function () use ($movements) {
            foreach ($movements as $movement) {
                $this->record(
                    product: $movement['product'],
                    type: $movement['type'],
                    quantity: $movement['quantity'],
                    notes: $movement['notes'] ?? null,
                    unitCost: $movement['unit_cost'] ?? null,
                    userId: $movement['user_id'] ?? null,
                    referenceType: $movement['reference_type'] ?? null,
                    referenceId: $movement['reference_id'] ?? null,
                );
            }
        });
    }

    /**
     * Get current stock for a product.
     */
    public function getStock(Product $product): int
    {
        return $product->stock;
    }

    /**
     * Get stock value (worth) for a product.
     */
    public function getStockValue(Product $product): float
    {
        return $product->stock * $product->purchase_price;
    }

    /**
     * Get total inventory value across all products.
     */
    public function getTotalInventoryValue(): float
    {
        return Product::query()
            ->where('track_stock', true)
            ->selectRaw('SUM(stock * purchase_price) as total')
            ->value('total') ?? 0;
    }

    /**
     * Get products with low stock.
     */
    public function getLowStockProducts()
    {
        return Product::where('track_stock', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get stock movement history for a product.
     */
    public function getHistory(Product $product, int $limit = 50)
    {
        return StockMovement::where('product_id', $product->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
