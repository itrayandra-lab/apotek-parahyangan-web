# Beautylatory - Project Context

## 1. Project Overview
**Beautylatory** is a Laravel 12-based e-commerce and content platform for beauty products. It features a dual-interface system: a public storefront for customers to browse products and articles, and a secure admin panel for content management.

*   **Core Purpose:** Showcase beauty products and educational articles.
*   **Key Features:** Role-based authentication (Admin/User), Product Catalog with categories & discounts, Blog/Article system with Rich Text & Scheduling, Homepage Slider, Glassmorphism UI.

## 2. Tech Stack & Environment
*   **Language:** PHP 8.4
*   **Framework:** Laravel 12
*   **Database:** MySQL (Production) / SQLite (Local Dev supported)
*   **Frontend:** Blade Templates + Tailwind CSS v4 + Alpine.js v3.15
*   **Build Tool:** Vite 7
*   **Testing:** PHPUnit 11.5

## 3. Architecture & Core Systems

### Authentication & Authorization
*   **Unified Login:** Single entry point (`/login`) accepts **Email OR Username**.
*   **Model:** Single `User` model with `role` column (`'admin'` or `'user'`).
*   **Guards:** `web` (default/customer) and `admin` (admin users). Both use the `User` model.
*   **Middleware:**
    *   `AdminAuth`: Protects `/admin/*` routes.
    *   `CustomerAuth`: Protects `/dashboard` and profile routes.

### Database Schema
*   **Primary Keys:** **Auto-increment Integer** (BigInt Unsigned). *Note: Migrated from UUIDs in Nov 2025.*
*   **Key Models:**
    *   `User`: Admin and Customer accounts.
    *   `Product`: Beauty items with pricing, discount, and category.
    *   `Category`: Product categories.
    *   `Article`: Blog posts with `status` (draft/scheduled/published), rich text body, and view tracking.
    *   `ArticleCategory` & `Tag`: Article organization.
    *   `Slider`: Homepage carousel images.

### Key Features Implementation

#### Products
*   **Images:** Managed via `spatie/laravel-medialibrary`. Auto-generates responsive variants.
*   **Listing:** Pagination (12 items), AJAX "Load More" functionality (Alpine.js + Axios).
*   **Pricing:** Supports `price` and nullable `discount_price`.

#### Articles (Blog)
*   **Rich Text:** Uses `tonysm/rich-text-laravel` (Trix Editor). Content stored in `rich_texts` table.
*   **Workflow:**
    *   `Draft`: Admin only.
    *   `Scheduled`: Publishes automatically via Laravel Scheduler at `scheduled_at`.
    *   `Published`: Publicly visible.
*   **Tags:** Auto-created inline during article editing.

## 4. Design System (Tailwind CSS v4)
*   **Configuration:** CSS-first via `resources/css/app.css` using `@theme`. **No `tailwind.config.js`**.
*   **Fonts:**
    *   Headings: **Playfair Display** (Serif, Luxury feel).
    *   Body: **Inter** (Sans-serif, UI/Reading).
*   **Colors (Semantic):**
    *   Primary: `rose-500` (#B76E79) - "Rose Gold"
    *   Dark: `gray-900` (#1A1A1A) - "Deep Charcoal"
    *   Tech Accent: `cyan-tech` (#00FFFF)
*   **UI Patterns:**
    *   **Glassmorphism:** `.glass-panel` (Backdrop blur + semi-transparent white/dark).
    *   **Components:** Reusable Blade components (e.g., `x-product-card`).

## 5. Development Workflow

### Common Commands
```bash
# Start Development Server (Concurrent: PHP, Vite, Queue, Logs)
composer run dev

# Run Tests
php artisan test

# Fix Code Style (Laravel Pint)
php artisan pint

# Database
php artisan migrate
php artisan db:seed
```

### Conventions
*   **Controllers:** Use FormRequests for validation (e.g., `ProductFormRequest`).
*   **Models:** Use mass assignment protection (`$fillable`).
*   **Frontend:** Mobile-first Tailwind classes. Use `gap-*` instead of margins for layout spacing.
*   **Comments:** Prefer self-documenting code. Use PHPDoc for complex logic.

## 6. Directory Structure Highlights
*   `app/Models`: Eloquent models.
*   `app/Http/Controllers/Admin`: Admin-specific controllers.
*   `resources/views/components`: Reusable Blade UI components.
*   `resources/css/app.css`: Main Tailwind v4 configuration and styles.
*   `routes/web.php`: Centralized route definitions.
*   `CLAUDE.md`: Detailed legacy documentation and specific implementation patterns.
*   `STYLE_GUIDE.md`: Comprehensive design tokens and UI documentation.
