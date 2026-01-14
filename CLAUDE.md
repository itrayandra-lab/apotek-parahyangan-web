# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Beautylatory** is a Laravel 12-based beauty product management system that allows retailers to showcase their products online with an easy-to-use admin interface. The application features a public-facing frontend for customers to browse and filter products by category, plus a secure admin panel for content management.

**Key Purpose:** Enable beauty retailers to manage their product catalog and promotional content efficiently while providing customers with an intuitive browsing experience.

**Current Stable Version:** Unified login system (email or username), role-based authentication (admin/customer), semantic color system, glass morphism design, rate-limited auth endpoints, auto-increment integer IDs (migrated from UUID), article management system with draft/scheduled/published workflow and rich text editing.

---

## Quick Commands

### Development
```bash
composer run dev
# Runs concurrently: php artisan serve, queue:listen, pail (logs), and npm run dev
# Server: http://localhost:8000
# Hot reload enabled for both frontend and backend

npm run build
# Build production assets with Vite

npm run dev
# Watch mode for frontend assets only
```

### Testing
```bash
composer run test
# Run all tests with PHPUnit

php artisan test tests/Feature/ProductControllerTest.php
# Run a single test file

php artisan test --filter=testMethodName
# Run a specific test
```

### Database
```bash
php artisan migrate
# Run all pending migrations

php artisan migrate:fresh --seed
# Reset database and run seeders

php artisan tinker
# Interactive PHP shell for testing queries
```

### Code Quality
```bash
php artisan pint
# Fix code style with Laravel Pint

php artisan pint --test
# Check code style without fixing
```

---

## Project Architecture

### Core Application Stack
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL (production), SQLite (local dev optional)
- **Frontend Build:** Vite 7 with Laravel Vite Plugin
- **Sessions:** Database-backed
- **Authentication:** Session-based with User model and role-based authorization (admin/user roles)

### Model-Controller-Route Pattern

**Models** (Database entities with auto-increment integer primary keys):
- `User` - Users with role-based authorization (roles: 'admin', 'user')
- `Product` - Beauty products with category relationship
- `Category` - Categories for products only
- `Slider` - Homepage carousel items
- `Article` - Blog articles with rich text content, status workflow (draft/scheduled/published), view counting
- `ArticleCategory` - Categories for articles with many-to-many relationship
- `Tag` - Tags for article tagging with many-to-many relationship and auto-creation support

**Controllers** (app/Http/Controllers):
- `ProductController` - Product CRUD (admin) + guest product listing with pagination/filtering
- `CategoryController` - Product category CRUD
- `SliderController` - Slider image management
- `AdminController` - Admin dashboard and profile management
- `CustomerController` - Customer dashboard and profile management
- `AuthController` - Unified login/logout/registration (supports email OR username login, role-based routing)
- `HomeController` - Landing page
- `ArticleController` - Public article listing with AJAX pagination, category filtering, article detail view with view count tracking
- `Admin\ArticleController` - Article CRUD with Trix editor, status management (draft/scheduled/published), publish/unschedule actions, dynamic tag creation
- `Admin\ArticleCategoryController` - Article category CRUD

**Form Requests** (app/Http/Requests - Laravel best practice for validation):
- Product/category/slider/article form requests with comprehensive validation rules and custom error messages

**Routes** (routes/web.php):
- Public routes: `/`, `/products`, `/articles`, `/articles/category/{slug}`, `/article/{slug}`
- Auth routes: `/login`, `/register`, `/logout` (unified authentication)
- Customer routes: `/dashboard`, `/profile` (protected by CustomerAuth middleware)
- Admin routes: `/admin/*` (protected by AdminAuth middleware)
- RESTful resource routes for admin CRUD operations:
  - Products, categories, sliders
  - Articles (with additional routes: `/admin/articles/{article}/publish`, `/admin/articles/{article}/unschedule`)
  - Article categories
- AJAX routes: `/products/load-more`, `/articles/load-more` (pagination support)

### Key Libraries

**Image Processing:**
- `spatie/laravel-medialibrary` (v11.17.5) - Auto-generates responsive image srcsets with optimal widths
- Images stored in: `storage/app/public/{model_id}/`
- Max file size: 2MB validation in controller
- Features: Progressive blur loading, responsive srcset, automatic width calculation

**Slugs & Rich Text:**
- `cviebrock/eloquent-sluggable` - Automatic slug generation for products, categories, articles, article categories, and tags
- `tonysm/rich-text-laravel` (v3.3) - WYSIWYG rich text editor (Trix) for article content storage in separate `rich_texts` table with HasRichText trait

**File Management:**
- **Products & Sliders:** Use Spatie Media Library with `withResponsiveImages()`
- No manual image conversions - Spatie auto-generates optimal sizes
- Auto-cleanup on model deletion (Media Library handles)

### Authentication Architecture

**Unified Login System:**
- Single login page at `/login` for both admin and customer authentication
- Supports login via **email OR username** (flexible credential input)
- Role-based redirection after successful authentication
- Session-based authentication with database-backed sessions (120-minute lifetime)

**User Model:**
- `User` model with auto-increment integer primary key (app/Models/User.php)
- **Columns:** `id` (bigint unsigned auto-increment), `name`, `username`, `email`, `password`, `role`, `email_verified_at`, `created_at`, `updated_at`
- **Roles:**
  - `'admin'` - Full access to admin panel (`/admin/*`)
  - `'user'` - Customer access to customer dashboard (`/dashboard`)
- **Methods:**
  - `isAdmin(): bool` - Returns true if `role === 'admin'`
  - `updateProfile(array $data): void` - Updates user profile

**Guard System:**
- `web` guard - Session-based for customers (role='user')
- `admin` guard - Session-based for admins (role='admin')
- Both guards use same User model and 'users' provider
- Configured in `config/auth.php`

**Middleware:**
- `AdminAuth` - Protects `/admin/*` routes, validates admin role
- `CustomerAuth` - Protects `/dashboard` and customer routes, validates user role
- Both registered in `bootstrap/app.php`

**Authentication Routes:**
```php
GET  /login           → Unified login form
POST /login           → Process login (email or username + password) [Rate limited: 5/min]
GET  /register        → Customer registration form
POST /register        → Process registration (creates user with role='user') [Rate limited: 5/min]
POST /logout          → Unified logout (detects guard automatically)
```

**Security Features:**
- Rate limiting on POST `/login` and POST `/register` (5 requests per minute per IP)
- CSRF protection on all forms
- Session regeneration after login
- Password hashing via bcrypt (automatic via `password` cast)

**Authentication Flow:**
1. User submits login form with email/username + password
2. `AuthController->login()` finds user by email OR username
3. Password validated using bcrypt hash comparison
4. Role determines guard and redirect:
   - `role='admin'` → Login via `admin` guard → Redirect to `/admin/dashboard`
   - `role='user'` → Login via `web` guard → Redirect to `/dashboard`
5. Session created and stored in database
6. Subsequent requests protected by role-specific middleware
7. Logout detects active guard and destroys session

**Registration Flow:**
- Only customers can self-register via `/register`
- Creates User with `role='user'` (default)
- Auto-login after successful registration
- Admin accounts must be created manually via database/seeding

---

## Common Development Tasks

### Adding a New Admin Resource (e.g., Reviews)

1. Create migration: `php artisan make:migration create_reviews_table`
2. Create model: `php artisan make:model Review --migration`
3. Add relationships in Model
4. Create controller: `php artisan make:controller ReviewController --resource`
5. Add route in `routes/web.php` under admin routes: `Route::resource('reviews', ReviewController::class);`
6. Create Blade templates in `resources/views/reviews/`
7. For images: Use `FileUploadService->store()` and `destroy()`

### Modifying Product Listing

- Guest product listing: `ProductController->guestIndex()` (pagination logic at line 45-50)
- Uses pagination (12 items for products)
- AJAX load-more: `ProductController->loadMore()` uses POST request with Axios + Alpine.js

### Product Card Component Pattern

**File:** `resources/views/components/product-card.blade.php`

A reusable Blade component for rendering product cards across the application:

```blade
<x-product-card :product="$product" :index="$loop->index" />
```

**Props:**
- `$product` (required) - Product model instance with category relationship
- `$index` (optional) - Array index for staggered animation delays

**Features:**
- Glass panel styling with hover elevation
- Responsive aspect ratio (3:4 for vertical products)
- Category badge with glass morphism
- Discount badge with percentage calculation
- Quick action button slides up on hover
- Product name with line clamping
- Price display with strikethrough for discounts
- All hover effects: image scale-110, overlay gradient, shadow enhancement

**Used in:**
- Initial server-render: `resources/views/products/index.blade.php` line 77
- AJAX render: Dynamically rendered via Alpine.js `renderProductCard()` method

### Alpine.js Load More Pattern

**File:** `resources/views/products/index.blade.php` (inline script, lines 128-241)

**Alpine Component:** `productLoader()` function

**State Properties:**
```javascript
{
    ajaxProducts: [],                    // Reactive array of AJAX-loaded products
    currentPage: 2,                      // Track pagination state
    initialProductCount: 12,             // Count of server-rendered products
    categoryFilter: '',                  // Active category filter (persisted)
    loading: false,                      // Loading state for button disable
    hasMorePages: true,                  // Pagination flag
}
```

**Methods:**

1. **`init()`** - Initializes the component (currently empty, hook for future setup)

2. **`loadMore()`** - Async method to fetch and append next page
   - Prevents double-loading with `if (this.loading || !this.hasMorePages) return`
   - POSTs to `/products/load-more` via Axios with page + category
   - Appends response products to `ajaxProducts` array (triggers `x-for` re-render)
   - Updates `hasMorePages` and `currentPage` state
   - Error handling with user-friendly alert
   - Finally block ensures loading state is cleared

3. **`renderProductCard(product, index)`** - Generates HTML for AJAX products
   - Calculates discount percentage: `((price - discount) / price) * 100`
   - Formats price with Indonesian locale: `new Intl.NumberFormat('id-ID').format(price)`
   - Builds complete product card HTML with all classes and structure
   - Calculates animation delay: `(index * 100) + 400` for staggered entrance
   - Returns HTML string for `x-html` binding

4. **`escapeHtml(text)`** - XSS protection utility
   - Prevents malicious script injection via product data
   - Uses DOM text node technique: `div.textContent = text; return div.innerHTML`

**Directives Used:**
- `x-data="productLoader()"` - Initializes Alpine component
- `x-init="init()"` - Calls init method on mount
- `x-for="(product, index) in ajaxProducts"` - Renders AJAX products dynamically
- `@click="loadMore()"` - Load more button click handler
- `x-show="loading"` - Shows/hides spinner during fetch
- `x-text="loading ? '...' : '...'"` - Dynamic button text
- `:disabled="loading"` - Disables button during request
- `x-html="renderProductCard(...)"` - Injects generated product card HTML
- `x-if="hasMorePages && initialProductCount > 0"` - Conditionally shows load button
- `x-if="!hasMorePages && ajaxProducts.length > 0"` - Shows end message

**SEO Optimization:**
- Initial 12 products rendered server-side (good for crawlers)
- AJAX products appended client-side (no duplicate content)
- Both use identical product card markup via component

### Working with Images

- All image uploads use Spatie Media Library via `addMediaFromRequest('image')->toMediaCollection()`
- Responsive images auto-render via `{{ $product->getImage() }}` in Blade templates
- Spatie auto-generates optimal srcset widths (no manual conversions needed)
- Progressive blur loading enabled by default
- Images stored in `storage/app/public/{model-id}/` with symlink to `public/storage/`

### Managing Hero Slider Images

The Splide.js carousel displays images from the Slider model. To manage hero slider:

**Adding a slider image:**
1. Use admin panel at `/admin/sliders` to upload image
2. Image is automatically converted to WebP format
3. `order` column determines display sequence (ascending)
4. Image is stored in `public/images/sliders/`

**Slider Model:**
- **Columns:** `id` (bigint unsigned auto-increment), `image` (filename), `order` (integer), `created_at`, `updated_at`
- **Ordering:** Sorted by `order` column ascending (in HomeController)
- **Files:** `app/Models/Slider.php`, database/migrations

**Carousel Configuration:**
- **Autoplay interval:** 5 seconds (change `interval: 5000` in home view)
- **Transition speed:** 1 second (change `speed: 1000`)
- **Easing function:** cubic-bezier(0.25, 1, 0.5, 1)
- **Navigation:** Arrows + pagination dots with glass morphism styling

**CSS customization:**
- Arrow styling: `.splide__arrow` in `resources/css/app.css` (lines 362-390)
- Pagination dots: `.splide__pagination__page` (lines 391-420)
- Mobile breakpoint: 640px (arrows hidden below this)

### Working with Rich Text Articles

**Package:** `tonysm/rich-text-laravel` v3.3

**Article Model Structure:**
- Uses `HasRichText` trait to manage rich text content stored in separate `rich_texts` table
- **Columns:** `id`, `title`, `slug`, `excerpt`, `author_id`, `status` (enum: draft/scheduled/published), `scheduled_at`, `published_at`, `views_count`, `created_at`, `updated_at`
- **Rich Text Attribute:** `$richTextAttributes = ['body']` - Stores full HTML content via Trix editor
- **Relationships:**
  - `author()` - BelongsTo User
  - `categories()` - BelongsToMany ArticleCategory via `article_article_category` pivot
  - `tags()` - BelongsToMany Tag via `article_tag` pivot with auto-creation support
  - Media: Uses Spatie Media Library for thumbnail (`article_images` collection)

**Article Status Workflow:**
1. **Draft** - Initial state, not visible to public (admin can save without publishing)
2. **Scheduled** - Article set for automatic publishing at `scheduled_at` datetime via Laravel scheduler
3. **Published** - Live and visible to public with `published_at` timestamp

**Publishing Methods:**
```php
$article->publish();              // Set status to published, set published_at to now()
$article->schedule($dateTime);    // Set status to scheduled with scheduled_at datetime
$article->unschedule();           // Revert scheduled article back to draft
```

**Query Scopes:**
```php
Article::published()        // Only published articles visible to public
Article::scheduled()        // Articles pending automatic publication
Article::draft()           // Draft articles only
Article::readyToPublish()  // Scheduled articles with scheduled_at <= now()
Article::popular()         // Order by views_count descending
Article::trending()        // Last 30 days, ordered by views
```

**Related Articles Feature:**
- `$article->getRelatedArticles(4)` returns up to 4 related published articles
- Algorithm: Prioritizes articles sharing the most tags
- Fallback: articles from same categories if no shared tags
- Sorted by shared tag count and popularity (views_count)

**Rich Text Content Handling:**
The HasRichText trait requires special handling in controllers - content must be set **after** model creation:

```php
// CORRECT - Extract body, create model, then set body
$bodyContent = $validated['body'];
unset($validated['body']);
$article = Article::create($validated);
$article->body = $bodyContent;  // Set rich text after creation
$article->save();

// WRONG - Don't include body in mass assignment
$article = Article::create($validated); // This won't save rich text content
```

**Rendering Rich Text:**
```blade
{!! $article->body !!}  // In public views
{!! $article->body->toTrixHtml() !!}  // For Trix editor (edit form)
```

**Thumbnail Image Management:**
- Single image per article via `article_images` media collection
- Upload: `$article->addMediaFromRequest('thumbnail')->toMediaCollection('article_images')`
- Access: `$article->getImageUrl()`, `$article->hasImage()`, `$article->getImage()`

**Automatic Publishing via Scheduler:**
```php
// routes/console.php
Schedule::command('articles:publish-scheduled')->hourly();

// app/Console/Commands/PublishScheduledArticles.php
Article::readyToPublish()->get()->each->publish();
```

**Admin Form Validation (ArticleFormRequest):**
- Title (required, max 255)
- Slug (optional, auto-generated from title, unique, lowercase with hyphens)
- Excerpt (optional, short summary)
- Body (required, HTML from Trix editor)
- Thumbnail (optional, image, max 2MB, jpeg/png/jpg/gif/webp)
- Categories (required, array, min 1)
- Tags (optional, array, auto-creates missing tags)
- Status (required, enum: draft/published/scheduled)
- Scheduled datetime (required if status=scheduled, must be future)
- Author ID (required, defaults to current user)

**Trix Editor Integration:**
```blade
<!-- Admin layout head -->
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
<script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

<!-- Form -->
<input id="body" type="hidden" name="body" value="{!! old('body', $article->body?->toTrixHtml()) !!}">
<trix-editor input="body" class="trix-content"></trix-editor>
```

**Custom Trix Styling:**
Custom CSS in `resources/css/app.css` for rich text display:
```css
.trix-content h1, .trix-content h2, .trix-content h3 {
  color: var(--color-text-primary) !important;
  font-weight: 700;
}
.trix-content p { color: var(--color-text-secondary) !important; }
.trix-content strong { color: var(--color-text-primary) !important; font-weight: 600; }
.trix-content a { color: var(--color-primary) !important; }
```

**Required Tailwind Plugin:**
```css
/* resources/css/app.css */
@import "tailwindcss";
@plugin "@tailwindcss/typography";  // Required for prose classes
```

### Working with Tags

Tags enable flexible article categorization alongside categories. Tags are automatically created when admins add new tag names during article creation/editing.

**Tag Model:**
- **Columns:** `id`, `name` (unique), `slug` (auto-generated), `created_at`, `updated_at`
- **Relationships:** `articles()` - BelongsToMany Article via `article_tag` pivot table
- **Usage:** Articles can have multiple tags; tags auto-created inline without pre-management

**Tag Auto-Creation Pattern:**
Dynamic tag creation in `Admin\ArticleController->handleTags()` method:
```php
private function handleTags(array $tagInputs): array {
    $tagIds = [];
    foreach ($tagInputs as $tagInput) {
        $tagInput = trim($tagInput);
        if (empty($tagInput)) continue;

        // Check if tag exists, create if not
        $tag = Tag::where('name', $tagInput)->first();
        if (!$tag) {
            $tag = Tag::create(['name' => $tagInput]);
        }
        $tagIds[] = $tag->id;
    }
    return $tagIds;
}

// In store/update methods
$article->tags()->sync($this->handleTags($validated['tags']));
```

This enables flexible tagging without requiring tag management UI.

### Database Relationships Reference

```php
// Product relationships
Product::with('category')->get()
Product::whereHas('category', fn($q) => $q->where('name', 'skincare'))->get()

// Article relationships
Article::published()->with(['author', 'categories', 'tags'])->get()
Article::whereHas('categories', fn($q) => $q->where('slug', 'beauty-tips'))->get()
Article::whereHas('tags', fn($q) => $q->where('name', 'skincare'))->get()
Article::whereHas('author', fn($q) => $q->where('name', 'Admin'))->get()

// Article query examples
Article::draft()->get()                  // Unpublished drafts
Article::scheduled()->get()              // Pending scheduled publishing
Article::readyToPublish()->get()         // Due for scheduler to publish
Article::popular()->limit(5)->get()      // Top 5 most viewed
Article::trending()->get()               // Last 30 days, most viewed
```

---

## Frontend Asset Pipeline

- **Build tool:** Vite 7
- **Entry point:** `resources/js/app.js`
- **Styling:** Compiled CSS in public/build/
- **Hot reload:** Enabled in dev mode
- **AJAX client:** Axios (for load-more product fetching)

---

## Important Configuration Files

- `.env` - Environment variables (copy from .env.example)
- `config/auth.php` - Auth guards and user providers (both guards use App\Models\User with role-based checking)
- `config/database.php` - Database configuration (MySQL for production)
- `config/seo.php` - SEO package settings
- `config/filesystems.php` - File storage (public disk points to public/images/)

---

## Testing Framework

- **Test Framework:** PHPUnit 11.5.3
- **Test Directories:** tests/Feature/, tests/Unit/
- **Factories:** database/factories/ (use Faker for test data)
- **Database Seeding:** database/seeders/ (used for test data)

---

## Common Patterns

### Filtering Products by Category
```php
$products = Product::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
    ->paginate(12);
```

### Image Upload with Cleanup
```php
// Delete old image
if ($model->image) {
    Storage::disk('public')->delete("images/products/{$model->image}");
}
// Upload new image
$model->image = $this->fileUploadService->store($request->file('image'), 'images/products');
```

---

## Frontend Design System

### Tailwind CSS 4 Architecture
**Main file:** `resources/css/app.css`
- CSS-first configuration using `@theme` directive (no `tailwind.config.js`)
- Import method: `@import "tailwindcss"` (Tailwind v4 syntax)
- Google Fonts integration in layout for Playfair Display + Inter

**Typography:**
- **Playfair Display** (serif) - Headings, luxury aesthetic
- **Inter** (sans-serif) - Body text, UI labels, accessibility

### Semantic Color System

**Purpose:** Centralized, maintainable color palette using semantic color aliases instead of hardcoded hex values.

**Color Tokens (defined in @theme):**

| Semantic Name | Hex Value | Primary Usage |
|---------------|-----------|---------------|
| `--color-primary` | #B76E79 | Primary brand accent, hover states, CTAs |
| `--color-primary-dark` | #9A5A63 | Darker primary for depth/shadows |
| `--color-primary-light` | #D49CA5 | Lighter primary for backgrounds/overlays |
| `--color-secondary` | #06B6D4 | Cyan tech accent |
| `--color-secondary-light` | #22D3EE | Lighter cyan for gradients |
| `--color-success` | #22C55E | Success states, confirmations |
| `--color-warning` | #EAB308 | Warning states, alerts |
| `--color-error` | #EF4444 | Error states, validation |
| `--color-text-primary` | #1A1A1A | Main text color |
| `--color-text-secondary` | #525252 | Secondary text |
| `--color-text-muted` | #A3A3A3 | Muted/disabled text |
| `--color-bg-primary` | #FAFAFA | Main page background |
| `--color-bg-secondary` | #F5F5F5 | Secondary backgrounds |

**Usage in Blade Views:**

```html
<!-- Instead of literal colors: -->
<button class="bg-rose-500 hover:bg-rose-600">Click</button>

<!-- Use semantic names: -->
<button class="bg-primary hover:bg-primary-dark">Click</button>
```

**Benefits:**
- Single source of truth for brand colors
- Easy rebranding (change @theme, all views update automatically)
- Semantically meaningful class names improve code readability
- Enables future dark mode support without view changes
- All views migrated to semantic names (footer, header, product-card, home, products)

### Component Patterns
- **Glass Morphism:** `.glass-panel` with rgba background + blur + border
- **Animations:** `.animate-scan`, `.animate-slow-spin`, `.animate-float`, `.animate-fade-in-up`
- **Responsive:** Mobile-first approach with gap utilities for spacing

### View Structure (Resources)
- **Layouts:** `layouts/app.blade.php` (main), `admin/layouts/app.blade.php` (admin)
- **Components:** header, footer, product-card (reusable)
- **Auth:** login, register
- **Admin:** products, categories, sliders (CRUD views)
- **Public:** home, products

### Interactive Features (Alpine.js)
- **Header scroll detection:** Glass panel appears after scrolling 20px
- **Mobile menu toggle:** Backdrop blur overlay, slide-in animation
- **Product carousel:** Smooth scroll buttons via `scrollContainer.scrollBy()`
- **Product grid filters:** Active state management for category buttons
- **Admin sidebar dropdowns:** Products menu has dropdown navigation with auto-open based on current route
- **Keyboard support:** Escape key closes mobile menu

### Extending the Design System

**Add Custom Colors:**
Edit `@theme` in `resources/css/app.css`:
```css
@theme {
  --color-custom-name: #HEXVALUE;
}
```

**Responsive:** Mobile-first (default), `md:` tablet+, `lg:` desktop+. Use gap utilities, not margins.

### Build & Deployment
**Development:**
```bash
npm run dev          # Watch mode, hot reload
composer run dev     # Concurrent: artisan serve + npm dev + pail
```

**Production:**
```bash
npm run build        # Optimize CSS/JS for production
php artisan serve    # Or deploy to server
```

---

## Important Implementation Details

### Image Upload Workflow

**For Products & Sliders (Spatie Media Library):**
1. User uploads image via form
2. Controller validates via Form Request (type: jpeg/png/jpg/gif/webp, size: 2MB max)
3. Image added to Media Library: `$model->addMediaFromRequest('image')->toMediaCollection('product_images')`
4. Spatie stores in `storage/app/public/{model-id}/` and auto-generates responsive widths
5. Auto-renders with srcset via `{{ $model->getImage() }}` in Blade templates
6. Auto-cleanup on model deletion (no manual deletion needed)

### Authentication Implementation
- Unified login at `/login` accepts email OR username
- `AuthController->login()` finds user via `User::where('email', $input)->orWhere('username', $input)`
- Password validation via `Hash::check($password, $user->password)`
- Role-based guard selection and redirect (admin → `/admin/dashboard`, user → `/dashboard`)
- Session stored in database (120-minute lifetime)
- Passwords auto-hashed via `password` cast on User model
- Use `auth()->user()` and `auth()->id()` instead of `Auth::guard('admin')` in views

### Form Request Validation Pattern
**Laravel Best Practice:** Always use Form Request classes instead of inline controller validation.

**Example Usage:**
```php
// Controller method signature
public function store(ProductFormRequest $request)
{
    $validated = $request->validated();
    // Create product with validated data
}
```

**Form Request Structure:**
```php
class ProductFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // ... more rules as needed
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            // ... custom messages
        ];
    }
}
```

**Dynamic Unique Validation:**
```php
// For update operations, exclude current record from unique check
$recordId = $this->route('category')?->id;
'name' => 'required|string|max:255|unique:categories,name,'.$recordId,
```

**Benefits:**
- Cleaner controllers (single responsibility)
- Reusable validation logic
- Custom error messages in one place
- Automatic validation before controller method executes

### Product Filtering
- Query parameter: `?category={category_id}`
- Maintains filters during AJAX load-more requests
- Respects pagination state (12 items per page)

### Database Schema Key Points
- All models use auto-increment bigint unsigned primary keys (migrated from UUID Nov 2025)
- Foreign key relationships use integer references
- Slug generation automated via eloquent-sluggable package
- Timestamps on all models (created_at, updated_at)

---

## Git Information

- **Main branch:** master
- **Default branch for PRs:** master
- **Recent architectural changes:**
  - Migrated UUID primary keys to auto-increment bigint integers (Nov 2025) - affects all models
  - Migrated from separate Admin model to User model with role-based authorization
  - **Complete article/blog module implementation** (Nov 2025):
    - Added Article, ArticleCategory, Tag models with full CRUD
    - Implemented rich text editing via tonysm/rich-text-laravel (HasRichText trait + Trix editor)
    - Status workflow: draft/scheduled/published with automatic scheduler support
    - Tag auto-creation during article management (no pre-management required)
    - Related articles feature using tag-based matching algorithm
    - Public article listing with AJAX pagination and category filtering
    - View tracking on article detail pages
    - Admin sidebar restructured with Articles dropdown navigation
    - Custom Trix content styling with @tailwindcss/typography plugin
- **Recent feature commits:**
  - Article module with rich text support and scheduled publishing
  - Product/category/slider CRUD improvements
  - Pagination improvements, sorting enhancements
  - Strikethrough pricing, discount features
  - Semantic color system implementation

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
- **PHP:** 8.4.13 | **Laravel:** v12 | **Tailwind:** v4 | **PHPUnit:** v11

## Conventions & Standards
- Follow existing code conventions in app (check sibling files for patterns)
- Use descriptive names (e.g., `isRegisteredForDiscounts` not `discount()`)
- Check for reusable components before creating new ones
- Don't change app structure/dependencies without approval
- If frontend changes don't reflect in UI, user should run `npm run build` or `composer run dev`


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Boost Tools
- **Artisan:** Use `list-artisan-commands` to check available parameters
- **URLs:** Use `get-absolute-url` for correct scheme/domain/port
- **Debugging:** Use `tinker` for PHP execution, `database-query` for read-only DB
- **Logs:** Use `browser-logs` for JS errors (recent logs only)
- **Docs:** Use `search-docs` before other approaches (auto-passes versions)


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>


