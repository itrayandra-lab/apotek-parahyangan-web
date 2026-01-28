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
            $table->string('invoice_number')->nullable()->after('order_number');
        });

        // Copy order_number to invoice_number for existing records
        DB::table('prescription_orders')->update([
            'invoice_number' => DB::raw('order_number')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_orders', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
};
