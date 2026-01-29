# Apotek Parahyangan Suite - Developer Handover Guide

> Last updated: December 2024
>
> This document is for developers taking over the Apotek Parahyangan Suite project.
> For AI assistant instructions, see `CLAUDE.md`.

---

## Project Overview

**Apotek Parahyangan Suite** is an e-commerce platform for beauty/skincare products built with Laravel 12.

### What It Does

- **Public storefront**: Product catalog, articles/blog, shopping cart, checkout
- **Customer accounts**: Registration, order history, multiple shipping addresses (max 5)
- **Admin panel**: Product/category management, orders, vouchers, site settings, AI chatbot config

### Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.4, Laravel 12 |
| Frontend | Blade templates, Alpine.js v3, Tailwind CSS v4 |
| Database | MySQL (sessions stored in DB) |
| Payment | Midtrans Snap (Indonesian payment gateway) |
| Shipping | RajaOngkir API (Indonesian shipping rates) |
| Media | Spatie Media Library (for Product, Article images) |
| Rich Text | Trix editor via tonysm/rich-text-laravel |

### Key Numbers

- 29 controllers (including Admin namespace)
- 21 Eloquent models
- Dual-guard auth system (customer + admin)

---

## Quick Start

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+ & npm
- MySQL 8+

### Installation

1. **Clone and install dependencies**

   ```bash
   git clone <repository-url>
   cd Apotek Parahyangan Suite_laravel
   composer install
   npm install
   ```

2. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure `.env`**

   ```env
   DB_DATABASE=Apotek Parahyangan Suite
   DB_USERNAME=your_user
   DB_PASSWORD=your_password

   # Midtrans (get from dashboard.midtrans.com)
   MIDTRANS_ENVIRONMENT=sandbox
   MIDTRANS_SERVER_KEY=SB-xxx
   MIDTRANS_CLIENT_KEY=SB-xxx
   MIDTRANS_MERCHANT_ID=xxx

   # RajaOngkir (get from rajaongkir.com)
   RAJAONGKIR_API_KEY=your_api_key
   RAJAONGKIR_ORIGIN_CITY_ID=your_city_id

   # Cart settings
   CART_PAYMENT_EXPIRY_HOURS=24
   PAYMENT_GATEWAY=midtrans
   ```

4. **Database setup**

   ```bash
   php artisan migrate --seed
   ```

5. **Storage link** (for uploaded images)

   ```bash
   php artisan storage:link
   ```

6. **Run development server**

   ```bash
   composer run dev
   ```

   This starts concurrently:
   - Laravel server at `http://localhost:8000`
   - Vite dev server (hot reload)
   - Queue listener
   - Log viewer (Pail)

### Default Admin Account

After seeding, login at `/login`. Check `database/seeders/UserSeeder.php` for credentials.

---

## Project Structure

### Key Directories

```
app/
├── Console/Commands/     # Artisan commands (ExpireUnpaidOrders, PublishScheduledArticles)
├── Http/
│   ├── Controllers/      # Public-facing controllers
│   │   └── Admin/        # Admin panel controllers
│   ├── Middleware/       # AdminAuth, CustomerAuth
│   └── Requests/         # Form Request validation classes
├── Models/               # Eloquent models
├── Services/             # Business logic
│   ├── Payment/          # MidtransService
│   ├── RajaOngkirService.php
│   └── VoucherService.php
├── Mail/                 # Email templates
├── Listeners/            # Event listeners
└── Policies/             # Authorization policies

resources/
├── views/
│   ├── admin/            # Admin panel views
│   ├── components/       # Reusable Blade components
│   ├── home/             # Public pages (landing, policies)
│   ├── products/         # Product listing
│   ├── articles/         # Blog/article pages
│   ├── cart/             # Shopping cart
│   ├── checkout/         # Checkout flow
│   └── layouts/          # Base layouts
└── css/app.css           # Tailwind v4 with @theme customizations

routes/
├── web.php               # All web routes (public + admin)
└── console.php           # Scheduled tasks

config/                   # App configuration
├── midtrans.php          # Payment gateway config
├── rajaongkir.php        # Shipping API config
└── auth.php              # Dual-guard configuration

bootstrap/app.php         # Middleware registration, CSRF exclusions
```

### Important Files

| File | Purpose |
|------|---------|
| `routes/web.php` | All route definitions |
| `bootstrap/app.php` | Middleware config, webhook CSRF exclusions |
| `config/auth.php` | Dual-guard configuration |
| `config/midtrans.php` | Payment gateway settings |
| `config/rajaongkir.php` | Shipping API settings |
| `resources/css/app.css` | Tailwind theme with semantic colors |

---

## Architecture Decisions

This section explains *why* certain patterns exist. Understanding these prevents introducing bugs.

### 1. Dual-Guard Authentication

**What:** Two separate auth guards - `web` for customers, `admin` for admin users.

**Why:**

- Same `User` model with `role` column
- Allows customer and admin to be logged in simultaneously
- Prevents accidental access - admin routes check `admin` guard, customer routes check `web` guard

**User Roles:**

| Role | Guard | Access |
|------|-------|--------|
| `admin` | admin | Full admin panel |
| `content_manager` | admin | Articles only |
| `user` | web | Customer features |

**Middleware accepts roles:**

```php
Route::middleware('admin.auth:admin')  // Admin only
Route::middleware('admin.auth')         // Admin OR content_manager
```

**Implication:**

- In shared components (header, footer), check BOTH guards:

  ```php
  $user = auth('web')->user() ?? auth('admin')->user();
  ```

- Logout must clear BOTH guards (see `AuthController@logout`)
- Never use `@auth` or `@guest` without specifying guard

---

### 2. Race Condition Protection

**What:** Stock decrements and voucher usage wrapped in `lockForUpdate()`.

**Why:** Concurrent requests (double-click, multiple tabs) can oversell products or double-use vouchers.

**Pattern:**

```php
DB::transaction(function () {
    $product = Product::lockForUpdate()->find($id);
    if ($product->stock < $quantity) {
        throw new Exception('Insufficient stock');
    }
    $product->decrement('stock', $quantity);
});
```

**Where:** `CheckoutController`, `VoucherService`

---

### 3. Server-Side Shipping Cost

**What:** Shipping cost is ALWAYS recalculated server-side via RajaOngkir API.

**Why:** Client-side shipping cost can be manipulated. Never trust the value sent from frontend.

**Where:** `CheckoutController@calculateServerSideShippingCost()`

---

### 4. Webhook Idempotency & Amount Verification

**What:** Payment webhooks check if already processed AND verify amount matches.

**Why:**

- Midtrans may send duplicate webhooks (network retries)
- Attackers could forge webhooks with manipulated amounts

**Pattern:**

```php
// Idempotency - skip if already processed
if ($order->status !== 'pending_payment') {
    return response()->json(['message' => 'Already processed']);
}

// Amount verification - reject if mismatch
if ($notification->gross_amount != $order->total) {
    Log::warning('Amount mismatch', [...]);
    return response()->json(['message' => 'Amount mismatch'], 400);
}
```

**Where:** `PaymentWebhookController`

**Also:** Webhook route excluded from CSRF in `bootstrap/app.php`.

---

### 5. Double-Submit Prevention

**What:** Checkout checks for existing pending orders before creating new one.

**Why:** User clicking "Pay" multiple times could create duplicate orders.

**Pattern:**

```php
$existingOrder = Order::where('user_id', $user->id)
    ->where('payment_status', 'unpaid')
    ->where('status', 'pending_payment')
    ->where('payment_expired_at', '>', now())
    ->whereNotNull('payment_url')
    ->lockForUpdate()
    ->first();

if ($existingOrder) {
    return redirect()->away($existingOrder->payment_url);
}
```

---

### 6. Form Requests for Validation

**What:** All validation in dedicated `FormRequest` classes, not inline in controllers.

**Why:**

- Reusable across store/update methods
- Custom error messages in one place
- Controllers stay clean

**Where:** `app/Http/Requests/`

---

### 7. Stock Restoration Logic

**What:** Stock is only restored when order transitions FROM specific states.

**Why:** Prevents double-restoration when webhook fires multiple times.

**Pattern:**

```php
$previousStatus = $order->status;
// ... update order status ...

$shouldRestoreStock = in_array($previousStatus, ['pending_payment', 'confirmed'], true);
if ($shouldRestoreStock) {
    $order->restoreStock();
}
```

---

## Key Flows

### Checkout & Payment Flow

```
1. Customer adds products to cart (CartController)
   ↓
2. Proceeds to checkout (/checkout)
   - Selects shipping address
   - Selects courier via RajaOngkir API
   - Applies voucher (optional)
   ↓
3. CheckoutController@process
   - Checks for existing pending order (double-submit prevention)
   - Validates stock (with lockForUpdate)
   - Recalculates shipping cost server-side
   - Creates Order + OrderItems in transaction
   - Decrements product stock
   - Marks voucher as used
   - Creates Midtrans Snap token
   ↓
4. Redirects to Midtrans payment page
   ↓
5. Customer completes payment
   ↓
6. Midtrans sends webhook to /payment/midtrans/notification
   - PaymentWebhookController verifies signature
   - Checks idempotency (skip if not pending)
   - Verifies amount matches
   - Updates order status
   - Sends email notification
```

### Order Statuses

| Status | Meaning |
|--------|---------|
| `pending_payment` | Awaiting payment |
| `confirmed` | Payment received |
| `processing` | Admin preparing order |
| `shipped` | Order dispatched |
| `delivered` | Completed |
| `cancelled` | Cancelled (stock restored) |
| `expired` | Payment timeout (stock restored) |

### Payment Statuses

| Status | Meaning |
|--------|---------|
| `unpaid` | No payment yet |
| `paid` | Payment successful |
| `failed` | Payment failed |
| `expired` | Payment expired |

### Order Number Format

```
ORD-YYYYMMDD-NNNN
Example: ORD-20241222-0001
```

### Scheduled Tasks

In `routes/console.php`:

| Command | Schedule | Purpose |
|---------|----------|---------|
| `articles:publish-scheduled` | Every minute | Auto-publish scheduled articles |
| `orders:expire-unpaid` | Hourly | Expire pending orders, restore stock |

---

## Critical Gotchas

Things that WILL break if you don't follow them.

### Never Do This / Always Do This

| Never | Always | Why |
|-------|--------|-----|
| `env('APP_NAME')` | `config('app.name')` | env() only works in config files |
| `DB::table('users')` | `User::query()` | Use Eloquent, not raw DB facade |
| `@auth` / `@guest` | Check both guards with PHP | Dual-guard system |
| Trust client shipping cost | Recalculate via RajaOngkir | Security |
| `$product->decrement('stock')` | Wrap in `lockForUpdate()` + transaction | Race conditions |
| Process webhook without status check | Check `if ($order->status !== 'pending_payment')` | Prevents duplicates |

### HasRichText (Article Body)

Body content must be set AFTER model creation:

```php
// CORRECT
$bodyContent = $validated['body'];
unset($validated['body']);
$article = Article::create($validated);
$article->body = $bodyContent;
$article->save();

// WRONG - body won't save
$article = Article::create(['body' => $bodyContent, ...]);
```

### OrderItem with Soft-Deleted Products

OrderItem uses `withTrashed()` to display deleted products in order history:

```php
public function product(): BelongsTo
{
    return $this->belongsTo(Product::class)->withTrashed();
}
```

### Guest Cart Merge

Guest session ID saved BEFORE login to enable cart merge:

```php
$guestSessionId = $request->session()->getId();
$request->session()->put('guest_cart_session_id', $guestSessionId);
Auth::guard('web')->login($user);
$request->session()->regenerate();  // New session ID
```

### Tailwind v4 Syntax

```css
/* WRONG - Tailwind v3 */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* CORRECT - Tailwind v4 */
@import "tailwindcss";
```

### Before Every Commit

```bash
vendor/bin/pint --dirty    # Fix code style
php artisan test           # Run tests
```

---

## External Services

### Midtrans (Payment Gateway)

**Dashboard:** https://dashboard.midtrans.com

**Configuration:** `config/midtrans.php` + `.env`

```env
MIDTRANS_ENVIRONMENT=sandbox      # or production
MIDTRANS_SERVER_KEY=SB-xxx
MIDTRANS_CLIENT_KEY=SB-xxx
MIDTRANS_MERCHANT_ID=xxx
```

**Key Files:**

- `app/Services/Payment/MidtransService.php` - Snap token creation
- `app/Http/Controllers/PaymentWebhookController.php` - Webhook handler

**Webhook Setup:**

1. In Midtrans dashboard → Settings → Configuration
2. Set Payment Notification URL: `https://yourdomain.com/payment/midtrans/notification`
3. Webhook is excluded from CSRF in `bootstrap/app.php`

---

### RajaOngkir (Shipping Rates)

**Dashboard:** https://rajaongkir.com

**Configuration:** `config/rajaongkir.php` + `.env`

```env
RAJAONGKIR_API_KEY=your_key
RAJAONGKIR_ORIGIN_CITY_ID=your_city_id
```

**Key Files:**

- `app/Services/RajaOngkirService.php` - API wrapper (destination search cached 24h)
- `app/Http/Controllers/ShippingController.php` - Shipping cost endpoints

**Supported Couriers:** JNE, SiCepat, J&T, Ninja, AnterAja, POS

---

### Spatie Media Library (Image Handling)

**What it does:**

- Handles image uploads with automatic responsive srcset
- Stores in `storage/app/public/{model-id}/`
- Auto-cleanup on model deletion

**Models using it:**

- `Product` - Collection: `product_images`
- `Article` - Collection: `article_images`
- `Slider` - Does NOT use Media Library (traditional file storage)

**Usage:**

```php
// Upload
$product->addMediaFromRequest('image')
    ->toMediaCollection('product_images');

// Display (in Blade)
{{ $product->getImage() }}  // Returns <img> with srcset
```

**Max upload size:** 2MB (validated in Form Requests)

---

## Rate Limiting

| Route | Limit |
|-------|-------|
| Login | 30/min |
| Register | 30/min |
| Cart add | 60/min |
| Contact form | 5/min |
| Chatbot | 30/min |

---

## Common Tasks

### Adding a New Admin Resource

Example: Adding a "Reviews" feature

```bash
# 1. Create model with migration and factory
php artisan make:model Review -mf

# 2. Create controller
php artisan make:controller Admin/ReviewController --resource

# 3. Create form request
php artisan make:request ReviewFormRequest

# 4. Add routes in routes/web.php (inside admin middleware group)
Route::resource('reviews', Admin\ReviewController::class);

# 5. Create views in resources/views/admin/reviews/
```

### Running Tests

```bash
# All tests
php artisan test

# Single file
php artisan test tests/Feature/CheckoutTest.php

# Single method
php artisan test --filter=test_checkout_validates_stock
```

### Database Operations

```bash
# Fresh migration with seed (resets everything)
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration add_rating_to_products_table

# Interactive database queries
php artisan tinker
>>> Product::where('stock', '<', 10)->get()
```

### Debugging

```bash
# Watch logs in real-time
php artisan pail

# Or check storage/logs/laravel.log
```

---

## Deployment Checklist

1. Set `MIDTRANS_ENVIRONMENT=production`
2. Update `MIDTRANS_SERVER_KEY` and `MIDTRANS_CLIENT_KEY` with production keys
3. Update webhook URL in Midtrans dashboard
4. Run `npm run build` for production assets
5. Run `php artisan config:cache`
6. Run `php artisan route:cache`
7. Run `php artisan view:cache`
8. Ensure scheduler is running: `* * * * * php artisan schedule:run`
9. Ensure queue worker is running for emails

---

## Key File Locations Summary

| Component | Location |
|-----------|----------|
| Auth Controller | `app/Http/Controllers/AuthController.php` |
| Checkout Controller | `app/Http/Controllers/CheckoutController.php` |
| Payment Webhook | `app/Http/Controllers/PaymentWebhookController.php` |
| Order Model | `app/Models/Order.php` |
| Cart Model | `app/Models/Cart.php` |
| Midtrans Service | `app/Services/Payment/MidtransService.php` |
| RajaOngkir Service | `app/Services/RajaOngkirService.php` |
| Voucher Service | `app/Services/VoucherService.php` |
| Admin Middleware | `app/Http/Middleware/AdminAuth.php` |
| Customer Middleware | `app/Http/Middleware/CustomerAuth.php` |
| Order Policy | `app/Policies/OrderPolicy.php` |
| Expire Orders Command | `app/Console/Commands/ExpireUnpaidOrders.php` |
| Publish Articles Command | `app/Console/Commands/PublishScheduledArticles.php` |

---

## Questions?

If something is unclear, check:

1. Existing code patterns - Look at similar features for reference
2. Laravel documentation - https://laravel.com/docs
