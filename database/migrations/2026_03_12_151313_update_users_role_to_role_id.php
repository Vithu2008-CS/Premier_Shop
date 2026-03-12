<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add role_id column (nullable initially for data migration)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        // 2. Migrate existing data: map role strings to role IDs
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        if ($adminRole) {
            DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRole->id]);
        }
        if ($customerRole) {
            DB::table('users')->where('role', 'customer')->update(['role_id' => $customerRole->id]);
        }
        // Any remaining users without a role_id, default to customer
        if ($customerRole) {
            DB::table('users')->whereNull('role_id')->update(['role_id' => $customerRole->id]);
        }

        // 3. Drop the old role string column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password');
        });

        // Migrate back
        $roles = DB::table('roles')->pluck('name', 'id');
        foreach ($roles as $id => $name) {
            DB::table('users')->where('role_id', $id)->update(['role' => $name]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
