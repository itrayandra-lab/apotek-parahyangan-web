<?php

namespace App\Listeners;

use App\Models\Cart;
use Illuminate\Auth\Events\Login;

class MergeGuestCartOnLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user || $user->role !== 'user') {
            return;
        }

        $guestSessionId = session()->pull('guest_cart_session_id', session()->getId());

        $guestCart = Cart::where('guest_session_id', $guestSessionId)->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::findOrCreateForUser($user);
        $userCart->mergeWith($guestCart);
    }
}
