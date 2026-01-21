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
            $table->string('order_number')->nullable()->after('id')->unique();
            $table->string('payment_url')->nullable()->after('total_price');
            $table->string('snap_token')->nullable()->after('payment_url');
            $table->string('payment_gateway')->default('midtrans')->after('snap_token');
            $table->string('payment_type')->nullable()->after('payment_gateway');
            $table->timestamp('payment_expired_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number',
                'payment_url',
                'snap_token',
                'payment_gateway',
                'payment_type',
                'payment_expired_at'
            ]);
        });
    }
};
