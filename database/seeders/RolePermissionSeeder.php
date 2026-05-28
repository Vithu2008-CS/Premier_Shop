<?php

/**
 * RolePermissionSeeder — Creates roles and assigns permission sets.
 * Roles: admin (all), manager (products/orders/coupons/sliders/reports),
 *        accountant (view-only + orders.update), driver (orders.view), customer (none).
 * Permissions grouped by module for display in the role editor UI.
 * Uses firstOrCreate so re-running is safe (idempotent).
 */

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Full system access — can manage everything.',
            'is_staff' => true,
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager'], [
            'display_name' => 'Manager',
            'description' => 'Can manage products, orders, categories, and view reports.',
            'is_staff' => true,
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant'], [
            'display_name' => 'Accountant',
            'description' => 'Can view orders, reports, and customer details.',
            'is_staff' => true,
        ]);

        $customer = Role::firstOrCreate(['name' => 'customer'], [
            'display_name' => 'Customer',
            'description' => 'Regular customer account.',
            'is_staff' => false,
        ]);
        $driver = Role::firstOrCreate(['name' => 'driver'], [
            'display_name' => 'Driver',
            'description' => 'Can manage assigned deliveries and update status.',
            'is_staff' => true,
        ]);

        // Create Permissions grouped by module
        $permissionGroups = [
            'Products' => [
                'products.view' => 'View Products',
                'products.create' => 'Create Products',
                'products.update' => 'Update Products',
                'products.delete' => 'Delete Products',
            ],
            'Categories' => [
                'categories.view' => 'View Categories',
                'categories.create' => 'Create Categories',
                'categories.update' => 'Update Categories',
                'categories.delete' => 'Delete Categories',
            ],
            'Orders' => [
                'orders.view' => 'View Orders',
                'orders.update' => 'Update Order Status',
                'orders.delete' => 'Delete Orders',
            ],
            'Customers' => [
                'customers.view' => 'View Customers',
                'customers.update' => 'Update Customers',
                'customers.delete' => 'Delete Customers',
            ],
            'Coupons' => [
                'coupons.view' => 'View Coupons',
                'coupons.create' => 'Create Coupons',
                'coupons.update' => 'Update Coupons',
                'coupons.delete' => 'Delete Coupons',
            ],
            'Sliders' => [
                'sliders.view' => 'View Sliders',
                'sliders.create' => 'Create Sliders',
                'sliders.update' => 'Update Sliders',
                'sliders.delete' => 'Delete Sliders',
            ],
            'Reports' => [
                'reports.view' => 'View Reports',
            ],
            'Settings' => [
                'settings.view' => 'View Settings',
                'settings.update' => 'Update Settings',
            ],
            'Roles' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.update' => 'Update Roles',
                'roles.delete' => 'Delete Roles',
            ],
            'Drivers' => [
                'drivers.view' => 'View Drivers',
                'drivers.create' => 'Create Drivers',
                'drivers.update' => 'Update Drivers',
                'drivers.delete' => 'Delete Drivers',
            ],
            'Returns' => [
                'returns.view' => 'View Returns',
                'returns.update' => 'Update Returns',
            ],
            'Reviews' => [
                'reviews.view' => 'View Reviews',
                'reviews.update' => 'Update Reviews',
                'reviews.delete' => 'Delete Reviews',
            ],
            'Shipping Rates' => [
                'shipping_rates.view' => 'View Shipping Rates',
                'shipping_rates.update' => 'Update Shipping Rates',
            ],
            'Mail' => [
                'mail.view' => 'View Mail Centre',
                'mail.manage' => 'Manage Mail Messages',
            ],
        ];

        $allPermissions = [];
        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $name => $displayName) {
                $allPermissions[] = Permission::firstOrCreate(['name' => $name], [
                    'display_name' => $displayName,
                    'group' => $group,
                ]);
            }
        }

        // Admin gets ALL permissions
        $admin->permissions()->sync(collect($allPermissions)->pluck('id'));

        // Manager gets a good subset
        $managerPerms = Permission::whereIn('name', [
            'products.view', 'products.create', 'products.update', 'products.delete',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',
            'orders.view', 'orders.update',
            'customers.view',
            'coupons.view', 'coupons.create', 'coupons.update',
            'sliders.view', 'sliders.create', 'sliders.update',
            'reports.view',
            'drivers.view', 'drivers.create', 'drivers.update',
            'returns.view', 'returns.update',
            'reviews.view', 'reviews.update', 'reviews.delete',
            'shipping_rates.view', 'shipping_rates.update',
            'mail.view', 'mail.manage',
        ])->pluck('id');
        $manager->permissions()->sync($managerPerms);

        // Accountant gets view-only + orders update
        $accountantPerms = Permission::whereIn('name', [
            'products.view',
            'orders.view', 'orders.update',
            'customers.view',
            'reports.view',
            'drivers.view',
            'returns.view',
            'reviews.view',
            'shipping_rates.view',
            'mail.view',
        ])->pluck('id');
        $accountant->permissions()->sync($accountantPerms);

        // Driver gets specific order permissions
        $driverPerms = Permission::whereIn('name', [
            'orders.view',
        ])->pluck('id');
        $driver->permissions()->sync($driverPerms);
    }
}
