<?php

/**
 * AdminUserSeeder — Idempotent admin provisioning for production.
 * Safe to run on EVERY deploy: ensures roles/permissions exist (RolePermissionSeeder
 * is idempotent), then creates-or-updates the single admin account. Uses
 * updateOrCreate keyed on email, so re-running never duplicates and always
 * resets the password to the known value.
 *
 * Run manually:  php artisan db:seed --class=AdminUserSeeder --force
 */

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure roles + permissions exist (idempotent).
        $this->call(RolePermissionSeeder::class);

        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $admin = User::updateOrCreate(
            ['email' => 'shopretail357@gmail.com'],
            [
                'name' => 'Shop Admin',
                'password' => bcrypt('12345678'),
                'dob' => '1990-01-01',
                'phone' => '07700000000',
                'address' => 'Premier Shop HQ, London, UK',
                'role_id' => $adminRole->id,
            ]
        );

        // Remove every OTHER admin account so this is the sole administrator.
        // FKs cascade (orders/addresses/reviews) or null out (audit logs), so
        // this deletes cleanly. The new admin above is excluded by id.
        User::where('role_id', $adminRole->id)
            ->where('id', '!=', $admin->id)
            ->get()
            ->each(fn (User $user) => $user->delete());
    }
}
