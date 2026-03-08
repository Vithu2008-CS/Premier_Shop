<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('offer_min_qty')->nullable()->after('is_active');
            $table->decimal('offer_discount_percent', 5, 2)->nullable()->after('offer_min_qty');
            $table->boolean('offer_active')->default(false)->after('offer_discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['offer_min_qty', 'offer_discount_percent', 'offer_active']);
        });
    }
};
