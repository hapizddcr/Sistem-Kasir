<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SaleTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;
    private Product $product;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role + permissions and assign
        $role = Role::create(['name' => 'cashier', 'guard_name' => 'web']);
        $permissions = ['view sales', 'create sales', 'view products', 'view customers', 'view stock'];
        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'guard_name' => 'web']);
        }
        $role->syncPermissions($permissions);
        $this->cashier = User::factory()->create();
        $this->cashier->assignRole('cashier');

        // Create product with stock
        $category = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);
        $unit = Unit::create(['name' => 'Botol', 'short_name' => 'btl']);
        $this->product = Product::create([
            'sku' => 'BEV-001',
            'barcode' => '8991234567890',
            'name' => 'Air Mineral 600ml',
            'slug' => 'air-mineral-600ml',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'purchase_price' => 3000,
            'selling_price' => 5000,
            'stock' => 50,
            'track_stock' => true,
            'is_active' => true,
        ]);

        // Create customer
        $this->customer = Customer::create([
            'code' => 'CUST-001',
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
        ]);
    }

    public function test_pos_page_loads_for_authenticated_cashier(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('sales.pos'));

        $response->assertOk();
        $response->assertViewIs('sales.pos');
        $response->assertViewHas('customers');
        $response->assertViewHas('featuredProducts');
    }

    public function test_pos_page_redirects_unauthenticated_to_login(): void
    {
        $response = $this->get(route('sales.pos'));

        $response->assertRedirect(route('login'));
    }

    public function test_sale_creation_deducts_stock_and_creates_sale_record(): void
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'unit_price' => 5000,
                    'discount' => 0,
                ],
            ],
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => 30000,
                ],
            ],
            'paid_amount' => 30000,
        ];

        $response = $this->actingAs($this->cashier)
            ->postJson(route('sales.store'), $payload);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['id', 'invoice_number', 'total'],
            'redirect',
        ]);

        // Verify sale created (total includes 11% tax on taxable products)
        $this->assertDatabaseHas('sales', [
            'customer_id' => $this->customer->id,
            'subtotal' => 25000,
            'status' => 'completed',
        ]);

        // Verify stock deducted
        $this->assertEquals(45, $this->product->fresh()->stock);

        // Verify sale item created
        $sale = Sale::first();
        $this->assertEquals(1, $sale->items()->count());
        $this->assertEquals(5, $sale->items()->first()->quantity);

        // Verify stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'sale',
            'quantity' => -5,
            'stock_before' => 50,
            'stock_after' => 45,
        ]);
    }

    public function test_sale_fails_when_stock_insufficient(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 999, // exceeds stock
                    'unit_price' => 5000,
                ],
            ],
            'paid_amount' => 5000000,
        ];

        $response = $this->actingAs($this->cashier)
            ->postJson(route('sales.store'), $payload);

        $response->assertStatus(422);
        $this->assertEquals(50, $this->product->fresh()->stock); // unchanged
        $this->assertEquals(0, Sale::count()); // no sale created
    }

    public function test_sale_invoice_number_is_generated(): void
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 5000,
                ],
            ],
            'payments' => [
                ['method' => 'cash', 'amount' => 5000],
            ],
            'paid_amount' => 5000,
        ];

        $this->actingAs($this->cashier)
            ->postJson(route('sales.store'), $payload);

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertNotNull($sale->invoice_number);
        $this->assertStringStartsWith('INV-', $sale->invoice_number);
    }
}
