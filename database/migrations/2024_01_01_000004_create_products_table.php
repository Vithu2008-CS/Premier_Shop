<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->text('images')->nullable();
            $table->enum('product_type', ['normal', 'wholesale'])->default('normal');
            $table->boolean('is_age_restricted')->default(false);
            $table->string('qr_code')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('offer_min_qty')->nullable();
            $table->decimal('offer_discount_percent', 5, 2)->nullable();
            $table->boolean('offer_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
