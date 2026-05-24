<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_connection_fee', 8, 2);
            $table->decimal('per_mile_rate', 8, 2);
            $table->decimal('per_kg_surcharge', 8, 2);
            $table->timestamps();
        });

        // Seed default rates so the application functions out-of-the-box
        DB::table('shipping_rates')->insert([
            'base_connection_fee' => 5.00,
            'per_mile_rate'       => 0.50,
            'per_kg_surcharge'    => 0.20,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
