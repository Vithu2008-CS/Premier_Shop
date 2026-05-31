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
            $table->decimal('offer_discount_percentage', 5, 2)->nullable()->after('loyalty_points');
            $table->string('offer_scope', 20)->nullable()->after('offer_discount_percentage'); // 'all' or 'selected'
            $table->json('offer_product_ids')->nullable()->after('offer_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['offer_discount_percentage', 'offer_scope', 'offer_product_ids']);
        });
    }
};
