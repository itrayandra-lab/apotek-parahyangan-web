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
            $table->char('province_code', 2)->nullable()->after('shipping_province');
            $table->char('city_code', 4)->nullable()->after('province_code');
            $table->char('district_code', 6)->nullable()->after('city_code');
            $table->char('village_code', 10)->nullable()->after('district_code');
            $table->string('shipping_district', 100)->nullable()->after('shipping_city');
            $table->string('shipping_village', 100)->nullable()->after('shipping_district');

            $table->index('province_code');
            $table->index('city_code');
            $table->index('district_code');
            $table->index('village_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['province_code']);
            $table->dropIndex(['city_code']);
            $table->dropIndex(['district_code']);
            $table->dropIndex(['village_code']);

            $table->dropColumn([
                'province_code',
                'city_code',
                'district_code',
                'village_code',
                'shipping_district',
                'shipping_village',
            ]);
        });
    }
};
