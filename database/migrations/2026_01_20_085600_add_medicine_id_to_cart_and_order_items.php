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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('medicine_id')->nullable()->after('product_id')->constrained('medicines')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('medicine_id')->nullable()->after('product_id')->constrained('medicines')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medicine_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medicine_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
