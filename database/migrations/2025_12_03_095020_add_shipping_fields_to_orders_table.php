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
            $table->string('shipping_courier', 50)->nullable()->after('shipping_cost');
            $table->string('shipping_service', 50)->nullable()->after('shipping_courier');
            $table->string('shipping_etd', 50)->nullable()->after('shipping_service');
            $table->string('shipping_awb', 100)->nullable()->after('shipping_etd');
            $table->unsignedInteger('rajaongkir_destination_id')->nullable()->after('shipping_awb');
            $table->unsignedInteger('shipping_weight')->default(0)->after('rajaongkir_destination_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_courier',
                'shipping_service',
                'shipping_etd',
                'shipping_awb',
                'rajaongkir_destination_id',
                'shipping_weight',
            ]);
        });
    }
};
