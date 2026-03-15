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
        // 1. Consolidated Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('Premier Shop');
            $table->string('origin_address')->nullable();
            $table->decimal('free_delivery_threshold', 10, 2)->default(50.00);
            $table->decimal('free_delivery_radius_miles', 10, 2)->default(10.00);
            $table->decimal('surcharge_per_mile', 10, 2)->default(1.50);
            $table->decimal('flat_rate_fee', 10, 2)->default(5.99);
            $table->json('other_settings')->nullable();
            $table->timestamps();
        });

        // 2. Consolidated Promotions Table (Includes Sliders/Banners)
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('button_text')->nullable();
            $table->enum('type', ['promotion', 'slider', 'banner'])->default('promotion');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order_priority')->default(0);
            $table->timestamps();
        });

        // 3. Consolidated User Tracking (Carts + Wishlists)
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->enum('type', ['cart', 'wishlist'])->default('cart');
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_items');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('settings');
    }
};
