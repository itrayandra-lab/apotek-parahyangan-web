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
        Schema::create('prescription_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_price', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->enum('pickup_status', ['waiting', 'ready', 'picked_up'])->default('waiting');
            $table->string('qr_code_token')->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'payment_status']);
            $table->index(['user_id', 'pickup_status']);
            $table->index('qr_code_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_orders');
    }
};
