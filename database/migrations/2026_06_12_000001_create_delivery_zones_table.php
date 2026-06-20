<?php

/**
 * Migration: zone-based delivery pricing.
 *
 * Replaces the old global shipping_rates engine (base fee + per-mile + per-kg)
 * with admin-defined distance zones: each zone covers a radius band in miles
 * and decides the fee from the order subtotal (fully free, free over a
 * threshold, or a flat zone fee).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('min_miles', 8, 2)->default(0);
            $table->decimal('max_miles', 8, 2);
            $table->boolean('is_free')->default(false);          // whole zone delivers free
            $table->decimal('free_over_amount', 10, 2)->nullable(); // free when subtotal >= this
            $table->decimal('delivery_fee', 10, 2)->default(0);   // charged otherwise
            $table->timestamps();
        });

        Schema::dropIfExists('shipping_rates');
    }

    public function down(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_connection_fee', 8, 2);
            $table->decimal('per_mile_rate', 8, 2);
            $table->decimal('per_kg_surcharge', 8, 2);
            $table->timestamps();
        });

        DB::table('shipping_rates')->insert([
            'base_connection_fee' => 5.00,
            'per_mile_rate' => 0.50,
            'per_kg_surcharge' => 0.20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::dropIfExists('delivery_zones');
    }
};
