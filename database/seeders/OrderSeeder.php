<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'customer@beautylatory.local')->first();

        if (! $user) {
            $user = User::factory()->create([
                'email' => 'customer@beautylatory.local',
                'username' => 'order_customer',
                'role' => 'user',
            ]);
        }

        $category = Category::firstOrCreate(
            ['slug' => 'orders'],
            ['name' => 'Orders', 'status' => 'active']
        );

        $product = Product::firstOrCreate(
            ['slug' => 'order-sample-product'],
            [
                'name' => 'Sample Product',
                'category_id' => $category->id,
                'price' => 200000,
                'discount_price' => null,
                'stock' => 100,
                'status' => 'published',
                'description' => 'Sample product used for order seeding.',
            ]
        );

        $quantity = 1;
        $subtotal = $product->price * $quantity;
        $total = $subtotal;

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'subtotal' => $subtotal,
            'shipping_cost' => 0,
            'total' => $total,
            'payment_gateway' => config('cart.default_gateway', 'midtrans'),
            'payment_type' => 'bank_transfer',
            'payment_status' => 'paid',
            'shipping_address' => 'Jl. Contoh No. 123, Jakarta',
            'shipping_city' => 'Jakarta',
            'shipping_province' => 'DKI Jakarta',
            'shipping_postal_code' => '12345',
            'phone' => '08123456789',
            'notes' => 'Sample seeded order.',
            'paid_at' => now(),
            'payment_external_id' => 'SEED-'.now()->format('YmdHis'),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ]);
    }
}
