<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@kasir.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '0812-1111-1111',
            ],
        );
        $admin->syncRoles(['admin']);

        // Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@kasir.local'],
            [
                'name' => 'Manager Toko',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '0812-2222-2222',
            ],
        );
        $manager->syncRoles(['manager']);

        // Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'kasir@kasir.local'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '0812-3333-3333',
            ],
        );
        $cashier->syncRoles(['cashier']);

        // Inventory
        $inventory = User::firstOrCreate(
            ['email' => 'gudang@kasir.local'],
            [
                'name' => 'Staf Gudang',
                'password' => Hash::make('password'),
                'is_active' => true,
                'phone' => '0812-4444-4444',
            ],
        );
        $inventory->syncRoles(['inventory']);
    }
}
