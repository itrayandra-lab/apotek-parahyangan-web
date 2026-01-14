<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('checkout.form'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_with_empty_cart_is_redirected_to_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('checkout.form'));

        $response->assertRedirect(route('cart.index'));
    }
}
