<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Suppliers
        $suppliers = [
            ['SUP001', 'PT Indofood Sukses Makmur', 'Budi Santoso', '021-12345678', 'budi@indofood.com'],
            ['SUP002', 'PT Unilever Indonesia', 'Siti Aminah', '021-23456789', 'siti@unilever.com'],
            ['SUP003', 'PT Sinar Mas', 'Joko Widodo', '021-34567890', 'joko@sinarmas.com'],
            ['SUP004', 'Toko Sumber Rezeki', 'Hendra Wijaya', '0812-3456-7890', null],
        ];
        foreach ($suppliers as [$code, $name, $cp, $phone, $email]) {
            Supplier::firstOrCreate(['code' => $code], [
                'name' => $name,
                'contact_person' => $cp,
                'phone' => $phone,
                'email' => $email,
                'address' => 'Jl. Industri No. ' . rand(1, 100),
                'city' => 'Jakarta',
                'is_active' => true,
            ]);
        }

        // Products
        $categoryIds = Category::pluck('id', 'name')->toArray();
        $unitId = Unit::where('short_name', 'pcs')->first()->id;

        $products = [
            ['Indomie Goreng Original', 'Makanan', 2500, 3500, 100],
            ['Indomie Kuah Soto', 'Makanan', 2500, 3500, 80],
            ['Pop Mie Goreng', 'Makanan', 5000, 7000, 50],
            ['Chitato 68g', 'Snack', 12000, 16500, 30],
            ['Tango Wafer Coklat', 'Snack', 8000, 11000, 40],
            ['Aqua 600ml', 'Minuman', 3500, 5000, 200],
            ['Teh Pucuk 350ml', 'Minuman', 4500, 6000, 150],
            ['Coca Cola 390ml', 'Minuman', 6500, 8500, 100],
            ['Beras Premium 1kg', 'Sembako', 14000, 18000, 50],
            ['Minyak Goreng 1L', 'Sembako', 18000, 23000, 60],
            ['Gula Pasir 1kg', 'Sembako', 16000, 21000, 70],
            ['Tepung Terigu 1kg', 'Sembako', 12000, 16000, 45],
            ['Sabun Mandi Lifebuoy', 'Toiletries', 4500, 6500, 80],
            ['Shampoo Pantene 70ml', 'Toiletries', 12000, 16500, 35],
            ['Pasta Gigi Pepsodent', 'Toiletries', 8000, 11500, 50],
            ['Surya 12', 'Rokok', 22000, 25000, 25],
            ['Gudang Garam Filter', 'Rokok', 24000, 27000, 30],
            ['Paracetamol Strip', 'Obat', 4500, 7000, 60],
            ['Vitamin C IPI', 'Obat', 8000, 12000, 40],
            ['Pulpen Standard', 'ATK', 3500, 5000, 100],
        ];

        foreach ($products as $i => [$name, $category, $buy, $sell, $stock]) {
            $sku = 'PRD' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT);
            Product::firstOrCreate(['sku' => $sku], [
                'barcode' => $sku,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(4),
                'category_id' => $categoryIds[$category] ?? $categoryIds['Makanan'],
                'unit_id' => $unitId,
                'purchase_price' => $buy,
                'selling_price' => $sell,
                'stock' => $stock,
                'min_stock' => 10,
                'is_active' => true,
                'is_taxable' => true,
                'track_stock' => true,
            ]);
        }
    }
}
