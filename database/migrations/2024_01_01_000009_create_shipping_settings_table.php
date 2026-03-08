<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('free_delivery_threshold', 10, 2)->default(50.00);
            $table->decimal('free_delivery_radius_miles', 10, 2)->default(10.00);
            $table->decimal('surcharge_per_mile', 10, 2)->default(1.50);
            $table->decimal('flat_rate_fee', 10, 2)->default(5.99);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
