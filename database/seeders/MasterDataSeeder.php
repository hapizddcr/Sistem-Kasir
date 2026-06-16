<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Membership;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Units
        $units = ['Pcs' => 'pcs', 'Box' => 'box', 'Pack' => 'pack', 'Kilogram' => 'kg', 'Liter' => 'L', 'Botol' => 'btl', 'Sachet' => 'sch', 'Karton' => 'krt'];
        foreach ($units as $name => $short) {
            Unit::firstOrCreate(['name' => $name], [
                'short_name' => $short,
                'description' => 'Satuan default: ' . $name,
                'is_active' => true,
            ]);
        }

        // Categories
        $categories = [
            ['Makanan', 'fa-utensils'],
            ['Minuman', 'fa-mug-hot'],
            ['Snack', 'fa-cookie'],
            ['Sembako', 'fa-shopping-basket'],
            ['Toiletries', 'fa-pump-soap'],
            ['Rokok', 'fa-smoking'],
            ['Obat', 'fa-pills'],
            ['ATK', 'fa-pen'],
        ];
        foreach ($categories as [$name, $icon]) {
            Category::firstOrCreate(['name' => $name], [
                'slug' => Str::slug($name),
                'icon' => $icon,
                'is_active' => true,
            ]);
        }

        // Memberships
        $memberships = [
            ['Bronze', 0, 0, 1, '#a16207', 'Pelanggan baru'],
            ['Silver', 1000000, 2, 2, '#94a3b8', 'Total belanja >= Rp 1 juta'],
            ['Gold', 5000000, 5, 3, '#eab308', 'Total belanja >= Rp 5 juta'],
            ['Platinum', 10000000, 10, 5, '#6366f1', 'Total belanja >= Rp 10 juta'],
        ];
        foreach ($memberships as [$name, $min, $disc, $mult, $color, $desc]) {
            Membership::firstOrCreate(['name' => $name], [
                'slug' => Str::slug($name),
                'min_spent' => $min,
                'discount_percent' => $disc,
                'points_multiplier' => $mult,
                'color' => $color,
                'description' => $desc,
                'sort_order' => $min,
                'is_active' => true,
            ]);
        }
    }
}
