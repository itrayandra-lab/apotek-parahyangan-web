<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
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
                'username' => 'cart_customer',
                'role' => 'user',
            ]);
        }

        $category = Category::firstOrCreate(
            ['slug' => 'skincare'],
            ['name' => 'Skincare', 'status' => 'active']
        );

        $product = Product::firstOrCreate(
            ['slug' => 'hydrating-serum'],
            [
                'name' => 'Hydrating Serum',
                'category_id' => $category->id,
                'price' => 150000,
                'discount_price' => 125000,
                'stock' => 50,
                'status' => 'published',
                'description' => 'Sample hydrating serum for cart seeding.',
            ]
        );

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 2]
        );
    }
}
