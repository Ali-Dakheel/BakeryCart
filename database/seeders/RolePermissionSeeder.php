<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Product management
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Order management
            'view orders',
            'update order status',
            'cancel orders',
            'view all orders', // admin only

            // Customer management
            'view customers',
            'edit customers',
            'delete customers',

            // Review management
            'view reviews',
            'moderate reviews',
            'respond to reviews',

            // Coupon management
            'view coupons',
            'manage coupons',

            // Settings
            'view settings',
            'manage settings',

            // Reports (future)
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission,
                'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //customer role
        $customer = Role::create(['name' => 'customer', 'guard_name' => 'web']);
        $customer->givePermissionTo([
            'view products',
            'view orders',
            'view reviews',
        ]);


        // staff role
        $staff = Role::Create(['name' => 'staff', 'guard_name' => 'web']);
        $staff->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view all orders',
            'update order status',
            'cancel orders',
            'view customers',
            'view reviews',
            'moderate reviews',
            'respond to reviews',
        ]);

        $admin = Role::Create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

    }
}
