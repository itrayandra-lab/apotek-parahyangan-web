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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('prescription_id')->nullable()->after('user_id')->constrained('prescriptions')->nullOnDelete();
            $table->unsignedBigInteger('refund_amount')->default(0)->after('total');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['prescription_id']);
            $table->dropColumn(['prescription_id', 'refund_amount']);
        });
    }
};
