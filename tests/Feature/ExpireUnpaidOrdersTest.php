<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireUnpaidOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_expire_command_marks_unpaid_orders_as_expired_and_restores_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'category_id' => $category->id,
            'price' => 100000,
            'discount_price' => null,
            'stock' => 5,
            'status' => 'published',
            'description' => 'Test',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'payment_gateway' => 'midtrans',
            'subtotal' => 200000,
            'tax' => 22000,
            'shipping_cost' => 0,
            'total' => 222000,
            'shipping_address' => 'Jl. Test',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI',
            'shipping_postal_code' => '12345',
            'phone' => '08123456789',
            'payment_expired_at' => now()->subHour(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => 2,
            'subtotal' => 200000,
        ]);

        // Simulate reserved stock
        $product->decrement('stock', 2);
        $product->refresh();
        $this->assertEquals(3, $product->stock);

        $this->artisan('orders:expire-unpaid')->assertExitCode(0);

        $order->refresh();
        $product->refresh();

        $this->assertEquals('expired', $order->status);
        $this->assertEquals('expired', $order->payment_status);
        $this->assertEquals(5, $product->stock);
    }
}
