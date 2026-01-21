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
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('sale_number')->unique();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('total_amount', 12, 2);
                $table->string('payment_status')->default('unpaid');
                $table->string('payment_method')->nullable();
                $table->string('order_type'); // 'shop' or 'prescription'
                $table->unsignedBigInteger('reference_id'); // ID from orders or prescription_orders
                $table->string('reference_number'); // order_number from orders or prescription_orders
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index(['order_type', 'reference_id']);
                $table->index('reference_number');
            });
        }

        if (!Schema::hasTable('sale_details')) {
            Schema::create('sale_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('item_name');
                $table->integer('quantity');
                $table->decimal('price', 12, 2);
                $table->decimal('subtotal', 12, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
        Schema::dropIfExists('sales');
    }
};
