<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;
    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->category = Category::create(['name' => 'Snack', 'slug' => 'snack']);
        $this->unit = Unit::create(['name' => 'Pack', 'short_name' => 'pack']);
    }

    public function test_admin_can_view_product_list(): void
    {
        Product::create([
            'sku' => 'SNK-001',
            'name' => 'Kerupuk Udang',
            'slug' => 'kerupuk-udang',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 8000,
            'selling_price' => 12000,
            'stock' => 100,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('products.index'));

        $response->assertOk();
        $response->assertViewIs('products.index');
        $response->assertSee('Kerupuk Udang');
        $response->assertSee('SNK-001');
    }

    public function test_admin_can_create_product(): void
    {
        $payload = [
            'sku' => 'SNK-002',
            'barcode' => '8999999999999',
            'name' => 'Chitato Original',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 50,
            'min_stock' => 10,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('products.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'SNK-002',
            'name' => 'Chitato Original',
            'selling_price' => '15000.00',
        ]);
    }

    public function test_product_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'category_id', 'unit_id', 'selling_price']);
    }

    public function test_product_creation_rejects_duplicate_sku(): void
    {
        Product::create([
            'sku' => 'DUP-001',
            'name' => 'First Product',
            'slug' => 'first-product',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $payload = [
            'sku' => 'DUP-001', // duplicate
            'name' => 'Second Product',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 6000,
            'selling_price' => 9000,
            'stock' => 5,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('products.store'), $payload);

        $response->assertSessionHasErrors('sku');
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::create([
            'sku' => 'UPD-001',
            'name' => 'Original Name',
            'slug' => 'original-name',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'stock' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('products.update', $product), [
                'sku' => 'UPD-001',
                'name' => 'Updated Name',
                'category_id' => $this->category->id,
                'unit_id' => $this->unit->id,
                'purchase_price' => 5500,
                'selling_price' => 9000,
                'stock' => 25,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'selling_price' => '9000.00',
            'stock' => 25,
        ]);
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $product = Product::create([
            'sku' => 'DEL-001',
            'name' => 'To Be Deleted',
            'slug' => 'to-be-deleted',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 5000,
            'selling_price' => 8000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('products.destroy', $product));

        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertRedirect(route('login'));
    }
}
