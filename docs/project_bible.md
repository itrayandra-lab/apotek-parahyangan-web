# Project Bible - Beautylatory

## Project Identity

-   **Name**: Beautylatory
-   **Core Value**: E-commerce skincare platform with AI chatbot integration
-   **Persona**: Premium beauty brand with scientific/biotech approach

## Tech Stack (DEFINITIVE)

-   **Backend**: PHP 8.4 / Laravel 12
-   **Frontend**: Blade + Alpine.js v3 + Tailwind CSS v4
-   **Database**: MySQL (sessions & auth database-backed)
-   **Payment**: Midtrans Snap
-   **Shipping**: RajaOngkir API
-   **Media**: Spatie Media Library
-   **Testing**: PHPUnit v11
-   **Code Style**: Laravel Pint

## Architecture Patterns

-   Form Requests for validation (not inline)
-   Eloquent relationships over raw queries
-   Named routes with `route()` helper
-   `config()` over `env()` outside config files
-   Constructor property promotion (PHP 8)
-   Explicit return types on all methods
-   Factories for test data
-   Pessimistic locking (`lockForUpdate()`) for concurrent operations (stock, vouchers)
-   Idempotency checks for webhook handlers
-   Dual-guard auth system: `web` guard for customers, `admin` guard for admin/content_manager
-   `redirectUsersTo` in bootstrap/app.php uses closure to check guard and redirect appropriately

## Key File Structure

-   `app/Http/Controllers/` - Controllers (admin in `Admin/` subfolder)
-   `app/Http/Requests/` - Form Request validation classes
-   `app/Models/` - Eloquent models
-   `app/Services/` - Business logic (e.g., `Payment/MidtransService`, `VoucherService`)
-   `app/Mail/` - Mailable classes
-   `app/Listeners/` - Event listeners (e.g., `MergeGuestCartOnLogin`)
-   `app/Console/Commands/` - Artisan commands (e.g., `ExpireUnpaidOrders`)
-   `resources/views/` - Blade views
-   `resources/views/components/` - Reusable Blade components (header, footer, cards)
-   `resources/views/admin/` - Admin panel views
-   `routes/web.php` - All web routes (frontend + admin)
-   `routes/console.php` - Scheduled tasks
-   `bootstrap/app.php` - Middleware config, CSRF exclusions
-   `config/` - App configuration files
-   `database/migrations/` - Database migrations
-   `tests/Feature/` - PHPUnit feature tests
-   `.kiro/skills/` - AI skill docs (midtrans, rajaongkir)

## NON-NEGOTIABLES (Anti-Patterns)

-   **NEVER** use `env()` directly outside config files
-   **NEVER** use `DB::` facade; prefer `Model::query()`
-   **NEVER** use deprecated Tailwind v3 utilities (use v4 replacements)
-   **NEVER** create verification scripts when tests cover functionality
-   **NEVER** use `@tailwind` directives; use `@import "tailwindcss"`
-   **NEVER** decrement stock without `lockForUpdate()` in transaction
-   **NEVER** process webhooks without idempotency check
-   **NEVER** restore stock without checking previous order status
-   **NEVER** use `@guest`/`@auth` without specifying guard (use PHP variables to check both guards)
-   **NEVER** use generic CSS class names like `.delay-100` that conflict with Tailwind utilities (namespace them, e.g., `.contact-delay-100`)
-   **NEVER** trust client-side shipping cost - always recalculate server-side via RajaOngkir API
-   **NEVER** mark payment as paid without verifying `gross_amount` matches `order->total`
-   **ALWAYS** run `vendor/bin/pint --dirty` before committing
-   **ALWAYS** use curly braces for control structures
-   **ALWAYS** use PHPDoc blocks over inline comments
-   **ALWAYS** exclude external webhook routes from CSRF in `bootstrap/app.php`
-   **ALWAYS** check both `admin` and `web` guards for auth state in shared components
-   **ALWAYS** logout from both guards in logout handler
-   **ALWAYS** check for existing pending orders before creating new checkout (double-submit prevention)

## Key Features Implemented

-   Cart/checkout with Midtrans payment (with race condition protection)
-   Multi-address per user (max 5)
-   Voucher/coupon system (with concurrent usage protection)
-   RajaOngkir shipping integration (server-side cost validation)
-   Article system with search
-   Product soft delete/restore
-   AI chatbot integration
-   Contact form with admin panel
-   Site settings (key-value store)
-   Email notifications (order lifecycle)
-   Global product search (header)
-   Guest cart merge on login
-   Scheduled order expiry (`orders:expire-unpaid`)
-   Double-submit prevention (redirects to existing pending payment)
-   Webhook amount verification (rejects mismatched amounts)

## UI/Design Patterns

-   Public pages use consistent design: gradient backgrounds, blur effects, `glass-panel` cards
-   Page headers: uppercase gradient text with `font-display`
-   Animations: `animate-fade-in-up` with staggered delays
-   Cards: `rounded-[2rem]` corners, `glass-panel` class
-   Buttons: rounded-full with hover effects and tracking-widest uppercase text
-   "Back to Home" navigation link on all public listing pages
