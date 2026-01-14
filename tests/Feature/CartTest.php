<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_product_to_cart(): void
    {
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
            'stock' => 10,
            'status' => 'published',
            'description' => 'Test description',
        ]);

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'count' => 2,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
