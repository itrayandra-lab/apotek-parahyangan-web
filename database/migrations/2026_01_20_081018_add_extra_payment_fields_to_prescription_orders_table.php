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
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->json('payment_callback_data')->nullable()->after('picked_up_at');
            $table->string('payment_external_id')->nullable()->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_callback_data', 'payment_external_id']);
        });
    }
};
