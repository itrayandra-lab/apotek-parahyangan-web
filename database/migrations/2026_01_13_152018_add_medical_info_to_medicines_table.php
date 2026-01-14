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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('classification')->nullable()->after('composition')->comment('Bebas, Bebas Terbatas, Obat Keras');
            $table->text('dosage')->nullable()->after('classification');
            $table->text('side_effects')->nullable()->after('dosage');
            $table->string('bpom_number')->nullable()->after('side_effects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['classification', 'dosage', 'side_effects', 'bpom_number']);
        });
    }
};
