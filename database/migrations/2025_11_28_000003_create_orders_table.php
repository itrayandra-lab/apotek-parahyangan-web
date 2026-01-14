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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'pending_payment',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'expired',
            ])->default('pending_payment');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('tax');
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('total');
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'failed',
                'expired',
                'refunded',
            ])->default('unpaid');
            $table->enum('payment_gateway', [
                'midtrans',
                'xendit',
                'doku',
                'manual',
            ])->default('midtrans');
            $table->string('payment_type', 50)->nullable();
            $table->string('payment_external_id')->nullable()->index();
            $table->text('payment_url')->nullable();
            $table->string('snap_token')->nullable();
            $table->timestamp('payment_expired_at')->nullable();
            $table->json('payment_callback_data')->nullable();
            $table->text('shipping_address');
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 10);
            $table->string('phone', 20);
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index('payment_expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
