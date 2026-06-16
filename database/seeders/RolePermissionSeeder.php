<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permissions = [
            // Products
            'view products', 'create products', 'edit products', 'delete products',
            // Categories & Units
            'manage master data',
            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',
            // Suppliers
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            // Sales
            'view sales', 'create sales', 'refund sales',
            // Purchases
            'view purchases', 'create purchases', 'receive purchases', 'cancel purchases',
            // Stock
            'view stock', 'adjust stock',
            // Reports
            'view reports', 'export reports',
            // Users
            'view users', 'create users', 'edit users', 'delete users',
            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view products', 'create products', 'edit products',
            'manage master data',
            'view customers', 'create customers', 'edit customers',
            'view suppliers', 'create suppliers', 'edit suppliers',
            'view sales', 'create sales', 'refund sales',
            'view purchases', 'create purchases', 'receive purchases', 'cancel purchases',
            'view stock', 'adjust stock',
            'view reports', 'export reports',
            'view users',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'view products',
            'view customers', 'create customers',
            'view sales', 'create sales',
            'view stock',
        ]);

        $inventory = Role::firstOrCreate(['name' => 'inventory', 'guard_name' => 'web']);
        $inventory->syncPermissions([
            'view products', 'create products', 'edit products',
            'manage master data',
            'view suppliers', 'create suppliers', 'edit suppliers',
            'view purchases', 'create purchases', 'receive purchases',
            'view stock', 'adjust stock',
        ]);
    }
}
