<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('postal_code');
        });

        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->dropColumn('origin_postal_code');
            $table->string('origin_address')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('postal_code')->nullable();
        });

        Schema::table('shipping_settings', function (Blueprint $table) {
            $table->string('origin_postal_code')->nullable();
            $table->dropColumn('origin_address');
        });
    }
};
