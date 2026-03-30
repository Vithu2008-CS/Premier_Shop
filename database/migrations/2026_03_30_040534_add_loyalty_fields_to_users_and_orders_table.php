<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('password');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('points_discount', 10, 2)->default(0)->after('discount_amount');
            $table->integer('points_used')->default(0)->after('points_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('loyalty_points');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_discount', 'points_used']);
        });
    }
};
