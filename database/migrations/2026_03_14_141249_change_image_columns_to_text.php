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
        Schema::table('products', function (Blueprint $table) {
            $table->text('images')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('delivery_proof')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('images')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_proof')->nullable()->change();
        });
    }
};
