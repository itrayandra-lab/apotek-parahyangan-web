<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_checkout_creates_pending_payment_order_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(['stock' => 5, 'price' => 100000]);

        $cart = Cart::findOrCreateForUser($user);
        $cart->addProduct($product, 2);

        $gateway = Mockery::mock(\App\Contracts\PaymentGatewayInterface::class);
        $gateway->shouldReceive('createTransaction')
            ->once()
            ->andReturn(['snap_token' => 'dummy-token']);
        $this->app->instance(\App\Contracts\PaymentGatewayInterface::class, $gateway);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'shipping_address' => 'Jl. Test',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_district' => 'Gambir',
            'shipping_village' => 'Cideng',
            'shipping_postal_code' => '12345',
            'province_code' => '31',
            'city_code' => '3173',
            'district_code' => '317301',
            'village_code' => '3173011001',
            'phone' => '08123456789',
            'notes' => 'Catatan',
            'shipping_courier' => 'jne',
            'shipping_service' => 'REG',
            'shipping_cost' => 15000,
        ]);

        $response->assertOk()->assertViewIs('checkout.payment');

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('pending_payment', $order->status);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals(5 - 2, $product->fresh()->stock);
        $this->assertEquals(0, $cart->fresh()->items()->count());
    }

    public function test_webhook_settlement_marks_paid_and_confirms_order(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(['stock' => 5, 'price' => 50000]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'payment_gateway' => 'midtrans',
            'subtotal' => 50000,
            'tax' => 5500,
            'shipping_cost' => 0,
            'total' => 55500,
            'shipping_address' => 'Jl. Test',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI',
            'shipping_postal_code' => '12345',
            'phone' => '08123456789',
            'payment_external_id' => 'ORD-TEST',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $gateway = Mockery::mock(\App\Contracts\PaymentGatewayInterface::class);
        $payload = [
            'order_id' => $order->order_number,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'status_code' => '200',
            'gross_amount' => (string) $order->total,
            'signature_key' => '',
        ];

        $gateway->shouldReceive('verifyNotification')->andReturn(true);
        $gateway->shouldReceive('parseNotification')->andReturn($payload);
        $this->app->instance(\App\Contracts\PaymentGatewayInterface::class, $gateway);

        $response = $this->postJson(route('payment.midtrans.notification'), $payload);
        $response->assertOk();

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('confirmed', $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_webhook_expire_restores_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct(['stock' => 5, 'price' => 50000]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'payment_gateway' => 'midtrans',
            'subtotal' => 100000,
            'tax' => 11000,
            'shipping_cost' => 0,
            'total' => 111000,
            'shipping_address' => 'Jl. Test',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI',
            'shipping_postal_code' => '12345',
            'phone' => '08123456789',
            'payment_external_id' => 'ORD-TEST-2',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => 2,
            'subtotal' => $product->price * 2,
        ]);

        // Reserve stock
        $product->decrement('stock', 2);
        $product->refresh();
        $this->assertEquals(3, $product->stock);

        $gateway = Mockery::mock(\App\Contracts\PaymentGatewayInterface::class);
        $payload = [
            'order_id' => $order->order_number,
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
            'status_code' => '200',
            'gross_amount' => (string) $order->total,
            'signature_key' => '',
        ];

        $gateway->shouldReceive('verifyNotification')->andReturn(true);
        $gateway->shouldReceive('parseNotification')->andReturn($payload);
        $this->app->instance(\App\Contracts\PaymentGatewayInterface::class, $gateway);

        $this->postJson(route('payment.midtrans.notification'), $payload)->assertOk();

        $order->refresh();
        $product->refresh();

        $this->assertEquals('expired', $order->payment_status);
        $this->assertEquals('expired', $order->status);
        $this->assertEquals(5, $product->stock);
    }

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'status' => 'active',
        ]);

        return Product::create(array_merge([
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'category_id' => $category->id,
            'price' => 100000,
            'discount_price' => null,
            'stock' => 10,
            'status' => 'published',
            'description' => 'Test description',
        ], $overrides));
    }
}
