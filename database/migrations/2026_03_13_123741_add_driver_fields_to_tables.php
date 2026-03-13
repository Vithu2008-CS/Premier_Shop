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
            $table->boolean('is_on_duty')->default(false)->after('role_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('delivery_proof')->nullable()->after('delivered_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_on_duty');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['driver_id', 'delivery_proof']);
        });
    }
};
