<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stockService;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = app(StockService::class);

        $category = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $unit = Unit::create(['name' => 'Pcs', 'short_name' => 'pcs']);
        $this->product = Product::create([
            'sku' => 'TEST-001',
            'name' => 'Test Product',
            'slug' => 'test-product',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'stock' => 100,
            'min_stock' => 10,
            'track_stock' => true,
            'is_active' => true,
        ]);
    }

    public function test_stock_in_increases_quantity_and_records_movement(): void
    {
        $movement = $this->stockService->record(
            product: $this->product,
            type: 'in',
            quantity: 50,
            notes: 'Restock from supplier',
        );

        $this->assertEquals(150, $this->product->fresh()->stock);
        $this->assertEquals(100, $movement->stock_before);
        $this->assertEquals(150, $movement->stock_after);
        $this->assertEquals(50, $movement->quantity);
        $this->assertEquals('in', $movement->type);
    }

    public function test_stock_out_decreases_quantity(): void
    {
        $this->stockService->record(
            product: $this->product,
            type: 'out',
            quantity: 30,
        );

        $this->assertEquals(70, $this->product->fresh()->stock);
    }

    public function test_stock_out_below_zero_throws_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stok tidak cukup');

        $this->stockService->record(
            product: $this->product,
            type: 'out',
            quantity: 9999,
        );
    }

    public function test_stock_adjustment_can_be_negative(): void
    {
        $movement = $this->stockService->record(
            product: $this->product,
            type: 'adjustment',
            quantity: -5,
            notes: 'Stock opname correction',
        );

        $this->assertEquals(95, $this->product->fresh()->stock);
        $this->assertEquals(-5, $movement->quantity);
    }

    public function test_zero_quantity_throws_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $this->stockService->record(
            product: $this->product,
            type: 'in',
            quantity: 0,
        );
    }

    public function test_invalid_type_throws_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $this->stockService->record(
            product: $this->product,
            type: 'invalid_type',
            quantity: 10,
        );
    }
}
