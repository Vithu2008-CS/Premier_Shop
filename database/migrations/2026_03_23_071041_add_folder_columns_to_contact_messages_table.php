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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('folder')->default('inbox')->after('is_read'); // inbox, sent, draft
            $table->boolean('is_starred')->default(false)->after('folder');
            $table->boolean('is_trash')->default(false)->after('is_starred');
            $table->string('tags')->nullable()->after('is_trash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['folder', 'is_starred', 'is_trash', 'tags']);
        });
    }
};
