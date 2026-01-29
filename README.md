# Apotek Parahyangan Suite

Premium e-commerce platform for skincare products with AI chatbot integration. Built with Laravel 12, Alpine.js, and Tailwind CSS v4.

## Tech Stack

- **Backend**: PHP 8.4 / Laravel 12
- **Frontend**: Blade Templates + Alpine.js v3 + Tailwind CSS v4
- **Database**: MySQL (sessions & auth database-backed)
- **Payment Gateway**: Midtrans Snap
- **Shipping**: RajaOngkir API
- **Media Management**: Spatie Media Library
- **Testing**: PHPUnit v11
- **Code Style**: Laravel Pint

## Key Features

### Customer Features
- Product catalog with search and filtering
- Shopping cart with guest support (merges on login)
- Multi-address management (max 5 per user)
- Real-time shipping cost calculation (RajaOngkir)
- Voucher/coupon system with usage limits
- Secure checkout with Midtrans payment
- Order tracking and history
- Article/blog system with search
- AI chatbot for product recommendations
- Contact form

### Admin Features
- Dashboard with order statistics
- Product management (CRUD with soft delete)
- Order management with status updates
- Voucher management
- Article management
- Contact form submissions
- Site settings (key-value configuration)
- Email notifications for order lifecycle

### Technical Highlights
- Race condition protection for stock and vouchers
- Idempotent webhook handling
- Pessimistic locking for concurrent operations
- Scheduled job for expiring unpaid orders
- Guest cart merge on login/registration
- CSRF-excluded webhook endpoints
- Comprehensive email notifications

## Installation

### Prerequisites
- PHP 8.4+
- Composer
- Node.js & npm
- MySQL
- Midtrans account (for payment)
- RajaOngkir API key (for shipping)

### Setup Steps

1. Clone the repository
```bash
git clone <repository-url>
cd Apotek Parahyangan Suite_laravel
```

2. Install dependencies
```bash
composer install
npm install
```

3. Environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure `.env` file with your credentials:
```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=false

RAJAONGKIR_API_KEY=your_rajaongkir_key
RAJAONGKIR_TYPE=starter

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
```

5. Run migrations and seeders
```bash
php artisan migrate --seed
```

6. Build frontend assets
```bash
npm run build
```

7. Create storage symlink
```bash
php artisan storage:link
```

## Development

### Running the Application

Start all development services (PHP server, queue worker, logs, and Vite):
```bash
composer run dev
```

Or run services individually:
```bash
# PHP development server
php artisan serve

# Frontend hot reload
npm run dev

# Queue worker (for emails and jobs)
php artisan queue:work

# Watch logs
tail -f storage/logs/laravel.log
```

### Testing

Run all tests:
```bash
composer run test
# or
php artisan test
```

Run specific test file:
```bash
php artisan test tests/Feature/CheckoutPaymentTest.php
```

Run with filter:
```bash
php artisan test --filter=testCheckoutWithVoucher
```

### Code Style

Auto-fix code style with Pint:
```bash
vendor/bin/pint --dirty
```

### Database Management

Fresh migration with seed data:
```bash
php artisan migrate:fresh --seed
```

Run specific seeder:
```bash
php artisan db:seed --class=ProductSeeder
```

### Scheduled Tasks

The application includes a scheduled job to expire unpaid orders. In production, add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or run manually:
```bash
php artisan orders:expire-unpaid
```

## Project Structure

```
app/
├── Console/Commands/      # Artisan commands (ExpireUnpaidOrders)
├── Http/
│   ├── Controllers/       # Application controllers
│   │   └── Admin/        # Admin panel controllers
│   └── Requests/         # Form request validation
├── Models/               # Eloquent models
├── Services/             # Business logic services
│   ├── Payment/         # MidtransService
│   └── VoucherService   # Voucher validation with locking
├── Mail/                # Mailable classes
└── Listeners/           # Event listeners (cart merge, etc.)

resources/
├── views/
│   ├── components/      # Reusable Blade components
│   ├── admin/          # Admin panel views
│   ├── products/       # Product pages
│   ├── checkout/       # Checkout flow
│   └── ...
├── js/                 # Alpine.js components
└── css/                # Tailwind CSS

routes/
├── web.php            # All web routes (frontend + admin)
└── console.php        # Scheduled tasks

database/
├── migrations/        # Database migrations
├── seeders/          # Database seeders
└── factories/        # Model factories

tests/
├── Feature/          # Feature tests
└── Unit/            # Unit tests
```

## API Integrations

### Midtrans Payment
- Snap integration for secure payment
- Webhook handler for payment notifications
- Automatic order status updates
- Stock restoration on payment failure/expiry

### RajaOngkir Shipping
- Real-time shipping cost calculation
- Support for multiple couriers (JNE, TIKI, POS)
- City and province data
- Weight-based pricing

## Security Features

- CSRF protection (with webhook exclusions)
- Database-backed sessions
- Rate-limited authentication
- Pessimistic locking for concurrent operations
- Idempotent webhook processing
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade escaping

## Contributing

1. Follow Laravel coding standards
2. Use Laravel Pint for code formatting
3. Write tests for new features
4. Update CHANGELOG.md with changes
5. Follow existing naming conventions

## License

Proprietary - All rights reserved

## Support

For issues or questions, please contact the development team.
